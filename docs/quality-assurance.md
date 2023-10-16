# Quality Assurance Process

Throughout the development of the library, new features and bug fixes will need to be tested.

## Prerequisites

You will need to install the `dist-archive` WP CLI command to create a plugin zip that QA technicians can install in various WordPress environments.

```sh
lando wp package install wp-cli/dist-archive-command
```

The `dist-archive` command provides production ready zips of plugin directories. [Learn More](https://developer.wordpress.org/cli/commands/dist-archive/)

## QA Preparation Process

*\*Assuming all relevant branches have been code reviewed and approved*
1. Deploy the [Server](https://github.com/stellarwp/telemetry-server) branch to the development environment and tag it with “Pending QA”
2. Push the feature branch for the Library to the repository and tag it with “Pending QA”
3. Set up any necessary functionality within the Library’s lando environment that’s necessary to validate the new feature (this should take place during feature development as a normal process)
4. Update the version number of the plugin in the environment following normal semantic versioning
5. Export the plugin as a zip with Lando:
```sh
lando wp dist-archive dev/public/wp-content/plugins/library-testing
```
6. Set up necessary Postman requests and parameters for export that QA can import and use to test the [Server](https://github.com/stellarwp/telemetry-server) changes
7. Upload Postman export and generated plugin zip from step 5
