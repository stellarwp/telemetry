# How to contribute to the Telemetry library

**Did you find a bug?**
- Search through [open issues](https://github.com/stellarwp/telemetry/issues) to **ensure the bug has not already been reported**.
- Once you've verified the issue has not been reported, [open a new issue](https://github.com/stellarwp/telemetry/issues/new). Please include a title and clear description, as much relevant information as possible, a code sample or testing steps to demonstrate the issue.

**Did you fix a bug?**
- Open a new GitHub pull request with the fix.
- Clearly describe the problem and solution in the PR's description field. If it fixes an open issue, please include the issue number.

## Development Workflow

The library uses a [gitflow process](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow) to coordinate releases and ongoing development.

<img src="docs/img/git-flow.svg" alt="A diagram showing the git branching structure for the library" width="700" style="display: block; margin: 25px auto;">

Branch all new code from [`develop`](https://github.com/stellarwp/telemetry/tree/develop) and once the feature or bugfix is complete, submit a pull request to be merged back into `develop`. **The PR will need to be code-reviewed and fully tested before it is merged**.

When a new release is planned, a new `release/[version]` branch will be created from the current develop branch. **No other PRs will be merged into the release branch.** It will be used to update all docblocks using `@since TBD` values and prep the library for a new release.

The library uses semantic versioning where the version number is represented as: `[major].[minor].[patch]`

- **Major**: This release will have breaking changes.
- **Minor**: This release includes new features and bugfixes.
- **Patch**: This release only includes bug fixes.

Release candidates will be represented as: `[major].[minor].[patch]-rc.[candidate-number]`
- The candidate number should have a leading 0 (i.e. `1.0.0-rc.01`)

**At this point, the library should be fully tested and documentation complete.**

When the release is complete, it will be merged into `main` and tagged with the version number to be updated on [Packagist](https://packagist.org/packages/stellarwp/telemetry) and in the [lastest releases](https://github.com/stellarwp/telemetry/releases).
