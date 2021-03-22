# How to contribute

Habemus loves to welcome your contributions. There are several ways to help out:
* Create a ticket in GitHub, if you have found a bug
* Write testcases for open bug tickets
* Write patches for open bug/feature tickets, preferably with testcases included
* Contribute to the [documentation](https://github.com/brenoroosevelt/habemus/tree/gh-pages)

There are a few guidelines that we need contributors to follow so that we have a
chance of keeping on top of things.

## Getting Started

* Submit a ticket for your issue, assuming one does not already exist.
  * Clearly describe the issue including steps to reproduce when it is a bug.
  * Make sure you fill in the earliest version that you know has the issue.
* Fork the repository on GitHub.

## Making Changes

* Create a topic branch from where you want to base your work.
* Make commits of logical units.
* Use descriptive commit messages and reference the #ticket number
* Core testcases should continue to pass.
* Your work should apply our coding standards.

## Submitting Changes

* Push your changes to a topic branch in your fork of the repository.
* Submit a pull request to the repository with the correct target branch.

## Testcases and codesniffer

To run the testcases locally use the following command:

    composer test
 
