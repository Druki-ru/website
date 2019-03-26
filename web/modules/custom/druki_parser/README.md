# Druki — Parser

This module parse content shipped via druki_git module to consumable data for further importing.

The parser looking for specific folder structure and file extensions.

Primarily it parses Markdown files with support of several custom structures created only for this parser and site to make content more flexible.

## Additional markup

### Meta information

Every doc file must contain meta information, this module adds special Markdown syntax for it. Meta information can be parsed only at the beginning of the file. No empty line before it is allowed.

```markdown
---
title: Hello World
id: hello-world
---
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

This information later parses to specifically structured arrays in `druki_parser.html` service.

### Internal link

The link to internal content by its id.

```markdown
{Link title}(content-id)
```

or

```markdown
{Link title}(content-id:core-version)
```

This will result this HTML markup:

```html
<a href="@druki_content:content-id">Link title</a>
```

Later this HTML processed by Text Filter plugin.

### Notes

There is custom markdown syntax to add notes to content. They will help to concentrate user on some important parts of the content.

There are several note types available to use:

 * `NOTE` — for simple notes to make simple attention.
 * `WARNING` — some additional information that must be taken to account by the reader.
 * `TIP` — advice for the user, that is not important, but can be helpful for someone.
 * `IMPORTANT` — information that must be read by everyone who read content.

The example of usage:

```markdown
> [!NOTE]
> This admin page is available when module [rest](some-link) is enabled.
>
> Notes support multiline and inline **markdown** syntax.
```
