Unhack the Core
===

An n98-magerun addon that detects changes to the Magento core and patches them in a rewrite.

Usage
---

We utilize two separate Magerun add-ons, `diff:files` and `core:unhack`. To install the core diff extension you will want to read the README for that utility. 

After installed and the clean core version is located in the correct directory you should be able to execute the following from within your Magento hacked core project directory:

```bash
magerun diff:files --line-numbers=1
```

With this in place you can pipe the output to the `core:unhack` Magerun module:

```bash
magerun diff:files --line-numbers=1 | magerun core:unhack
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

GNU GENERAL PUBLIC LICENSE
Version 3, 29 June 2007