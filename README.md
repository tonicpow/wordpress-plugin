<img src="./.github/IMAGES/github-share-image-v2.png?raw=true" alt="TonicPow Wordpress Plugin">
<br />

> Learn more [about TonicPow](https://tonicpow.com/?utm_source=github&utm_medium=sponsor-link&utm_campaign=wordpress-plugin&utm_term=wordpress-plugin&utm_content=wordpress-plugin). Checkout the [TonicPow API Docs](https://docs.tonicpow.com).

[![last commit](https://img.shields.io/github/last-commit/tonicpow/wordpress-plugin.svg?style=flat&v=3)](https://github.com/tonicpow/wordpress-plugin/commits/master)
[![version](https://img.shields.io/github/release-pre/tonicpow/wordpress-plugin.svg?style=flat&v=3)](https://github.com/tonicpow/wordpress-plugin/releases)
[![php](https://img.shields.io/badge/php-7.4.3-blue.svg?v=3)](https://www.php.net/downloads)
[![wordpress](https://img.shields.io/badge/wordpress-5.6.2-blue.svg?v=3)](https://wordpress.org/download/)
[![Sponsor](https://img.shields.io/badge/sponsor-TonicPow-181717.svg?logo=github&style=flat&v=3)](https://github.com/sponsors/TonicPow)
[![slack](https://img.shields.io/badge/slack-tonicpow-orange.svg?style=flat&v=3)](https://atlantistic.slack.com/app_redirect?channel=tonicpow)

## Table of Contents

- [Download](#download)
- [Installation](#installation)
- [Features](#features)
- [Developer Resources](#developer-resources)
- [Code Standards](#code-standards)
- [Maintainers](#maintainers)
- [Contributing](#contributing)
- [License](#license)

<br/>

## Download

The latest release will always be available to download at this link:
[https://tonicpow.com/wordpress/tonicpow.zip](https://tonicpow.com/wordpress/tonicpow.zip)
  
<br/>

## Installation

- Follow the [wordpress guidelines](https://wordpress.org/support/article/managing-plugins/) for installing the plugin.

<br/>

## Features

- Trigger a `conversion` on an action (IE: order)
- Record the `tncpw_session` of the visitor
- Display a `widget`

<br/>

## Developer Resources

Checkout the [Wordpress Plugin Guide](https://tonicpow.com/guides/developers/wordpress-plugin), and the [TonicPow API Docs](https://docs.tonicpow.com)

<details>
<summary><strong><code>Package Dependencies</code></strong></summary>
<br/>

- [Wordpress](https://wordpress.com/)
- [WooCommerce](https://woocommerce.com/)
</details>

<details>
<summary><strong><code>Library Deployment</code></strong></summary>
<br/>

[goreleaser](https://github.com/goreleaser/goreleaser) for easy binary or library deployment to Github and can be installed via: `brew install goreleaser`.

The [.goreleaser.yml](.goreleaser.yml) file is used to configure [goreleaser](https://github.com/goreleaser/goreleaser).

Use `make release-snap` to create a snapshot version of the release, and finally `make release` to ship to production.

</details>

<details>
<summary><strong><code>Makefile Commands</code></strong></summary>
<br/>

View all `makefile` commands

```shell script
make help
```

List of all current commands:

```text
all                  Runs multiple commands
clean                Remove previous builds and any test cache data
help                 Show this help message
release              Full production release (creates release in Github)
release-test         Full production test release (everything except deploy)
release-snap         Test the full release (build binaries)
replace-version      Replaces the version in HTML/JS (pre-deploy)
tag                  Generate a new tag and push (tag version=0.0.0)
tag-remove           Remove a tag if found (tag-remove version=0.0.0)
tag-update           Update an existing tag to current commit (tag-update version=0.0.0)
```

</details>

<br/>

## Code Standards

Read more about this PHP project's [code standards](CODE_STANDARDS.md).

<br/>

## Maintainers

| [<img src="https://github.com/rohenaz.png" height="50" alt="Satchmo" />](https://github.com/rohenaz) | [<img src="https://github.com/mrz1836.png" height="50" alt="MrZ" />](https://github.com/mrz1836) |
| :--------------------------------------------------------------------------------------------------: | :----------------------------------------------------------------------------------------------: |
|                                [Satchmo](https://github.com/rohenaz)                                 |                                [MrZ](https://github.com/mrz1836)                                 |

<br/>

## Contributing

View the [contributing guidelines](CONTRIBUTING.md) and follow the [code of conduct](CODE_OF_CONDUCT.md).

### How can I help?

All kinds of contributions are welcome :raised_hands:!
The most basic way to show your support is to star :star2: the project, or to raise issues :speech_balloon:.
You can also support this project by [becoming a sponsor on GitHub](https://github.com/sponsors/tonicpow) :clap:
or by making a [**bitcoin donation**](https://tonicpow.com/?utm_source=github&utm_medium=sponsor-link&utm_campaign=wordpress-plugin&utm_term=wordpress-plugin&utm_content=wordpress-plugin) to ensure this journey continues indefinitely! :rocket:

<br/>

## License

![License](https://img.shields.io/github/license/tonicpow/wordpress-plugin.svg?style=flat&v=3)
