<?php

namespace Drupal\druki_search\SearchPage;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo refactor or remove. Fore refactoring the form needs to have cache for
 *   search filter results.
 */
class FilterForm extends FormBase {

  /**
   * The query helper.
   *
   * @var \Drupal\druki_search\SearchPage\QueryHelper
   */
  protected $queryHelper;

  public function __construct(QueryHelper $query_helper) {
    $this->queryHelper = $query_helper;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('druki_search.page.query_helper')
    );
  }

  public static function afterBuild($form) {
    foreach (['form_id', 'form_build_id', 'form_token', 'op'] as $element) {
      unset($form[$element]);
    }

    return $form;
  }

  public static function alterQuery(QueryInterface $query) {
    $request = Drupal::request();

    $filter_group = $query->createConditionGroup('AND');
    if ($request->query->has('difficulty')) {
      $filter_group->addCondition('difficulty', $request->query->get('difficulty', []), 'IN');
    }

    if ($request->query->has('core')) {
      $filter_group->addCondition('core', $request->query->get('core'));
    }

    $query->addConditionGroup($filter_group);
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'druki_search_page_filter';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;

    $form['difficulty'] = $this->buildFilterCheckboxes('difficulty', new TranslatableMarkup('Difficulty'));
    // Make difficulty labels user-riendly.
    $form['difficulty']['filter']['#options'] = array_map(function ($option) {
      switch ($option) {
        case 'none':
          return new TranslatableMarkup('Not set');

        default:
          return $option;
      }
    }, $form['difficulty']['filter']['#options']);

    $form['core'] = $this->buildFilterCheckboxes('core', new TranslatableMarkup('Core version'));
    // Make core labels user-riendly.
    $form['core']['filter']['#options'] = array_map(function ($option) {
      switch ($option) {
        case 'none':
          return new TranslatableMarkup('Not applicable');

        case '8':
          return 'Drupal 8';

        case '9':
          return 'Drupal 9';

        default:
          return $option;
      }
    }, $form['core']['filter']['#options']);
    ksort($form['core']['filter']['#options']);

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Apply filters'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  protected function buildFilterCheckboxes(string $element_name, string $title): ?array {
    $index_options = $this->getOptionsFromResults($element_name, $element_name);
    $options = array_map(function ($item) {
      return $item['label'];
    }, $index_options);

    if (!count($options)) {
      return NULL;
    }

    $form['filter'] = [
      '#type' => 'checkboxes',
      '#title' => $title,
      '#options' => $options,
      '#parents' => [$element_name],
      '#default_value' => $this->getRequest()->query->get($element_name, []),
    ];

    return $form;
  }

  protected function getOptionsFromResults(string $value_field, string $name_field): array {

    $options = [];
    $results = $this->getResultsFromIndex();
    /** @var \Drupal\search_api\Item\Item $result */
    foreach ($results as $result) {
      if ((bool) !$result->getField($value_field)->getValues()) {
        continue;
      }
      $id = $result->getField($value_field)->getValues()[0];
      $name = $result->getField($name_field)->getValues()[0];
      $options[$id] = [
        'id' => $id,
        'label' => $name,
      ];
    }

    return $options;
  }

  protected function getResultsFromIndex() {
    $results = $this->queryHelper->getQuery()->execute()->getResultItems();

    return $results;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $request = $this->getRequest();
    $query = [];
    $query['text'] = $request->query->get('text');

    foreach ([
      'difficulty',
      'core',
    ] as $filter) {
      $filter_name = str_replace('_', '-', $filter);
      $filter_value = $form_state->getValue($filter);
      if (is_array($filter_value)) {
        // Reset keys.
        $filter_value = array_values($filter_value);
        // Remove 0 values.
        $filter_value = array_filter($filter_value, function ($value) {
          return $value ?: FALSE;
        });
      }
      $query[$filter_name] = $filter_value;
    }

    // Clean up.
    $query = array_filter($query, function ($value) {
      if (is_array($value) && empty($value) || !$value) {
        return FALSE;
      }

      return TRUE;
    });

    $form_state->setRedirect('<current>', [], ['query' => $query]);
  }

}
