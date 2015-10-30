Unhack the Core
===

An [n98-magerun](https://github.com/netz98/n98-magerun) addon that detects changes to the Magento core and patches them in a rewrite.

Installation
---
There are a few options.  You can check out the different options in the [MageRun
docs](http://magerun.net/introducting-the-new-n98-magerun-module-system/).

Here's the easiest:

1. Create ~/.n98-magerun/modules/ if it doesn't already exist.

        mkdir -p ~/.n98-magerun/modules/

2. Clone the core:unhack repository in there

        cd ~/.n98-magerun/modules/
        git clone git@github.com:talesh/unhack-core.git


Usage
---

We utilize two separate Magerun add-ons, `diff:files` (which is part of [Kalen Jordan's Magerun Addons](github.com/kalenjordan/magerun-addons)) and `core:unhack`. To install the core diff extension you will want to read the README for that utility. 

After installed and the clean core version is located in the correct directory you should be able to execute the following from within your Magento hacked core project directory:

```bash
mr diff:files --line-numbers=1
```

With this in place you can pipe the output to the `core:unhack` Magerun module:

```bash
mr diff:files --line-numbers=1 | mr core:unhack
```

This will interrogate the core, find changes, and migrate them to a core rewrite module under the module name which can be found in `app/code/local/Migrated/FromCore`.


Roadmap
---

There are a number of features that will be developed but are not currently available:

- Controller rewrite support
- Abstract class core hack support
- Update Magento core with pristine version post-patch
- lib/Zend or other lib overrides


License
---

Magerun Addons - Core Unhack
Copyright (C) 2015 Philwinkle LLC / Phillip Jackson

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.