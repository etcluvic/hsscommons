# HSSCommons Overlay

This repository contains code modified for the HSSCommons project by CANARIE. It acts as an overlay to the [HUBzero CMS](https://github.com/hubzero/hubzero-cms) code to customize HUBzero for use by HSSCommons.****

The HSSCommons git repository consists of two main branches: `test` and `h2.2.22`.

The `test` branch is associated with the [test instance of the HSSCommons site](https://test.hsscommons.ca).

# Installation

This code should be placed in the /app folder of the HUBzero CMS. It is currently developed against version 2.2.22 of HUBzero.

# Contributing to HSSCommons

1. Open a terminal session and clone the `test` branch using `git clone -b test https://github.com/etcluvic/hsscommons.git`
2. Run a `git pull` to update your local repository with any changes that any other collaborators have made.
3. Make any changes to the `test` branch as follows. 
4. Edit the files as required. Use `git add <file name>` to stage these changes.
5. Use `git commit -m '<commit message>'` to commit your changes.
6. Use `git push --set-upstream origin test` to push your commits to the remote repo.
7. Once the feature has been fully debugged and confirmed to be working as expected, the changes from the `test` branch can be merged with the `h2.2.22` branch.
8. Open a browser window with the URL of the HSSCommons git repo. Click on pull requests and create a new one.
9. Compare the changes between the `test` branch and the `h2.2.22` branch. The changes you just pushed on the `test` branch should be highlighted. Verify the changes and create the pull request. Make sure to write a descriptive comment.
10. Verify that there are no conflicts, proceed an merge the request. Do not delete the `test` branch when asked if you want to delete it.

To have the h2.2.22 instance reflect the changes in the `h2.2.22` branch, we need to create a ticket with UVic Systems.

# Licence

This code maintains the [MIT](http://opensource.org/licenses/MIT) licence of the original files.
