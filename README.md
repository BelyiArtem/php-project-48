# GenDiff

### Hexlet tests and linter status

[![Actions Status](https://github.com/BelyiArtem/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/BelyiArtem/php-project-48/actions)
[![Gen Diff](https://github.com/BelyiArtem/php-project-48/actions/workflows/differ.yml/badge.svg)](https://github.com/BelyiArtem/php-project-48/actions/workflows/differ.yml)

[![Code Quality](https://img.shields.io/badge/Code%20Sniffer-Passing-green)](https://github.com/squizlabs/PHP_CodeSniffer)
![Dependencies](https://img.shields.io/badge/Dependencies-Up--to--date-brightgreen.svg)
![Last Commit](https://img.shields.io/github/last-commit/BelyiArtem/php-project-48)
![License](https://img.shields.io/badge/License-MIT-green)

[![Quality gate status](https://sonarcloud.io/api/project_badges/measure?project=BelyiArtem_php-project-48&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=BelyiArtem_php-project-48)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=BelyiArtem_php-project-48&metric=bugs)](https://sonarcloud.io/summary/new_code?id=BelyiArtem_php-project-48)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=BelyiArtem_php-project-48&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=BelyiArtem_php-project-48)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=BelyiArtem_php-project-48&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=BelyiArtem_php-project-48)
---

## Description

**GenDiff** is a command-line utility that compares two configuration files and displays the difference.

Supported formats:

- JSON
- YAML (.yml/.yaml)

Supported output formats:

- `stylish`
- `plain`
- `json`

---

## Installation

Clone the repository:

```bash
git clone https://github.com/BelyiArtem/php-project-48.git
cd php-project-48
```

Install dependencies:

```bash
make install
```

---

## Usage

```bash
./bin/gendiff <filepath1> <filepath2>
```

Specify output format:

```bash
./bin/gendiff --format plain file1.json file2.json
```

or

```bash
./bin/gendiff -f plain file1.json file2.json
```

---

## Examples

### Compare two JSON files

[![asciicast](https://asciinema.org/a/LP2UH0QkF1LTQviO.svg)](https://asciinema.org/a/LP2UH0QkF1LTQviO)

---

### Compare two YAML files

[![asciicast](https://asciinema.org/a/gCDrg2oegE2kVPhJ.svg)](https://asciinema.org/a/gCDrg2oegE2kVPhJ)

---

### Plain output format

[![asciicast](https://asciinema.org/a/2wIr4g10jMDtNqJg.svg)](https://asciinema.org/a/2wIr4g10jMDtNqJg)

---

### Compare two JSON files

[![asciicast](https://asciinema.org/a/4hvEMz9TQkgjvlbH.svg)](https://asciinema.org/a/4hvEMz9TQkgjvlbH)

---

## Development

Run linter:

```bash
make lint
```

Run tests:

```bash
make test
```

Run tests with coverage:

```bash
make test-coverage
```