# Druki — Parser

This module parse content shipped via druki_git module to consumable data for further importing.

The parser looking for specific folder structure and file extensions.

Primarily it parse Markdown files with support of several custom structures created only for this parser and site to make content more flexible.

## Additional markup

### Meta information

Every doc file must contains meta information, this module adds special Markdown syntax for it.

```markdown
...
title: Hello World
id: hello-world
...
```

This block can contains any data in format `{KEY}: {VALUE}`:

- `{KEY}` - `[a-zA-Z0-9]` string only.
- `{VALUE}` - any string value.

This will result this HTML markup:

```html
<div data-druki-meta="">
    <div data-druki-key="title" data-druki-value="Hello World">title: Hello World</div>
    <div data-druki-key="id" data-druki-value="hello-world">id: hello-world</div>
</div>
```

This information later parses to specific structured arrays in `druki_parser.html` service.

### Internal link

The link to internal content by its id.

```markdown
{Link title}(content-id)
```

This will result this HTML markup:

```html
<a href="@druki_content:content-id">Link title</a>
```

Later this HTML processed by Text Filter plugin.
