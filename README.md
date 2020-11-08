# TonicPow: Wordpress Plugin
> The official plugin for interacting with the TonicPow API

[![Release](https://img.shields.io/github/release-pre/tonicpow/wordpress-plugin.svg?logo=github&style=flat&v=2)](https://github.com/tonicpow/wordpress-plugin/releases)
[![Build Status](https://travis-ci.com/tonicpow/wordpress-plugin.svg?branch=master&v=2)](https://travis-ci.com/tonicpow/wordpress-plugin)

<br/>

## Table of Contents
- [Installation](#installation)
- [Documentation](#documentation)
- [Examples & Tests](#examples--tests)
- [Benchmarks](#benchmarks)
- [Code Standards](#code-standards)
- [Usage](#usage)
- [Maintainers](#maintainers)
- [Contributing](#contributing)
- [License](#license)

<br/>

## Installation

**TODO**

<br/>

## Documentation
**TODO**

### Features
**TODO**

<details>
<summary><strong><code>Package Dependencies</code></strong></summary>
<br/>

**TODO**
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

## Examples & Tests
**TODO**

<br/>

## Benchmarks
**TODO**

<br/>

## Code Standards
Read more about this Go project's [code standards](CODE_STANDARDS.md).

<br/>

## Usage
**TODO**

<br/>

## Maintainers
| [<img src="https://github.com/rohenaz.png" height="50" alt="Satchmo" />](https://github.com/rohenaz) | [<img src="https://github.com/mrz1836.png" height="50" alt="MrZ" />](https://github.com/mrz1836) |
|:---:|:---:|
| [Satchmo](https://github.com/rohenaz) | [MrZ](https://github.com/mrz1836) |

<br/>

## Contributing

View the [contributing guidelines](CONTRIBUTING.md) and follow the [code of conduct](CODE_OF_CONDUCT.md).

### How can I help?
All kinds of contributions are welcome :raised_hands:! 
The most basic way to show your support is to star :star2: the project, or to raise issues :speech_balloon:. 
You can also support this project by [becoming a sponsor on GitHub](https://github.com/sponsors/tonicpow) :clap: 
or by making a [**bitcoin donation**](https://tonicpow.com/?af=wordpress-plugin) to ensure this journey continues indefinitely! :rocket:

<br/>

## License

![License](https://img.shields.io/github/license/tonicpow/wordpress-plugin.svg?style=flat&v=2)
