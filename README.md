# MATA-Framework
 
=======
MATA Framework
==========================================

The basic foundation block for all MATA Applications. This is where it all begins.


Installation
------------

- Add the module using composer: 

```json
"mata/mata-framework": "~1.0.0"
```

- An even better way is to check out a whole MATA application to start building your solution: 
```json
composer create-project --prefer-source mata/mata-application=~1.0.0 NEW_PROJECT_NAME
```


Changelog
---------

## 1.0.9-alpha, June 6, 2015

- Prevented saving models when ordering in [[RearrangeAction]] 

## 1.0.8-alpha, June 2, 2015

- ItemOrderBehavior updated next() and previous() methods

## 1.0.7-alpha, May 28, 2015

- Added [[forceIncrement]] to [[IncrementalBehavior]]
- Ensured [[EVENT_BEFORE_PREPARE_STATEMENT]] fires only once per model
- New way of handling Media, allowing versioning and deletions
- Updated [[StrongLinkBehavior]] to accept closures in [[StrongLinkBehavior::links]]


## 1.0.6-alpha, May 26, 2015

- Modified error message markups and added strong tags

## 1.0.5-alpha, May 26, 2015

- Added [[StrongLinkBehavior]]

## 1.0.4-alpha, May 26, 2015

- Added [[ItemOrderBehavior]]

## 1.0.3.1-alpha, May 25, 2015

- Fixing bugs introduced in 1.0.3-alpha

## 1.0.3-alpha, May 25, 2015

- Added [[ActiveQuery]] with [[cachedOne]] method
- Removed unused files

## 1.0.2-alpha, May 22, 2015

- FineUploader view updated.

## 1.0.1-alpha, May 19, 2015

- Added JS trigger for 'mediaChanged' event for FineUploader after file delete.

## 1.0.0-alpha, May 18, 2015

- Initial release.
