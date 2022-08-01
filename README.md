# ACLs from files

This TYPO3 extension allows to put the values of the following fields of table `be_groups` into external yaml files:

* `non_exclude_fields`
* `explicit_allowdeny`
* `pagetypes_select`
* `tables_select`
* `tables_modify`
* `groupMods`
* `availableWidgets`
* `file_permissions`

## How does it work?

When calculating the "permissions" for the current BE user (see `BackendUserAuthentication->fetchGroups()`) a post-process hook will resolve file references for each BE group to external yaml files and add their contents to the comma separated fields mentioned above.

## Important hint

Keep in mind: this is not an override mechanism but an addition of comma separated values!

So be sure to clear the values of the above mentioned fields when selecting an external file.

This might help:

```sql
UPDATE be_groups SET non_exclude_fields=NULL,explicit_allowdeny=NULL,pagetypes_select=NULL,tables_select=NULL,tables_modify=NULL,groupMods=NULL,availableWidgets=NULL,file_permissions=NULL WHERE tx_aclsfromfiles_file <> '';
```

## Export existing ACLs

To export the ACLs of an existing group to a yaml file call this:

```
bin/typo3 acls_from_files:export <group> [--dry-run] [--verbose]
```

This command:

* creates a new yaml file within the `config/acls/` folder containing the ACL fields of the given group
* sets `tx_aclsfromfiles_file` to that new file
* empties the values of the above mentioned fields
