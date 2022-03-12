# HSSCommons Overlay

This repository contains code modified for the HSSCommons project by CANARIE. It acts as an overlay to the [HUBzero CMS](https://github.com/hubzero/hubzero-cms) code to customize HUBzero for use by HSSCommons.****

The HSSCommons git repository consists of two main branches: `test` and `prod`.

The `test` branch is associated with the [test instance of the HSSCommons site](https://test.hsscommons.ca).

# Installation

This code should be placed in the /app folder of the HUBzero CMS. It is currently developed against version 2.2.22 of HUBzero.

# Contributing to HSSCommons

1. Open a terminal session and clone the `test` branch using `git clone -b test https://github.com/etcluvic/hsscommons.git`
2. Create a new branch locally. Branches should be named after the associated tasks WBS ID like `1.1.12`
3. Edit the files as required. Use `git add <file name>` to stage these changes.
4. Use `git commit -m '<commit message>'` to commit your changes.
5. Use `git push --set-upstream origin <branch name>` to push your commits to the remote repo.
6. Open a browser window with the URL of the HSSCommons git repo. Click on pull requests and create a new one.
7. Compare the changes between the new branch you pushed and the `test` branch. The changes you just pushed should be highlighted. Verify the changes and create the pull request. Make sure to write a descriptive comment.
8. Verify that there are no conflicts, proceed an merge the request. Delete/archive any old branches.
9. Check if the changes happened as expected on the test instance.
10. Debug any issues by following this same process to update the code.

Once the feature has been fully debugged and confirmed to be working as expected, the changes from the `test` branch can be merged with the `prod` branch.

To have the prod instance reflect the changes in the `prod` branch, we need to create a ticket with UVic Systems.

# Licence

This code maintains the [MIT](http://opensource.org/licenses/MIT) licence of the original files.