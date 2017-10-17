# php-set-env
This tool helps setting `.env` file configs via the command line.

## Install
We recommend installing the package via composer.

```
composer require softbricks/php-set-env
```

After that, it is most practical to create a composer script (within `composer.json`) for easier usage:

```JSON
{
    "scripts": {
        "set-env": [
            "SoftBricks\\CLI\\SetEnv::execute"
        ]
    }
}
```


## Usage

Let's say we have a `.env` file that looks like this ...
```
FOO=BAR

[CATEGORY1]
NESTED_PARAM=Hi, I am nested
```

... and we want reset the value of `FOO`. This can be done with:

```
composer set-env -- --key=FOO --value="not bar anymore"
```

This call results in the following `.env` file:

```
FOO=not bar anymore

[CATEGORY1]
NESTED_PARAM=Hi, I am nested
```

It is also possible to add new key / value pairs, with the same call structure. For example:

```
composer set-env -- --key=NEW --value="wow I'm so new"
```

To set the `NESTED_PARAM` within `CATEGORY1` we have to structure our call like this:

```
composer set-env -- --category=CATEGORY1 --key=NESTED_PARAM --value="Changed nested param"
```