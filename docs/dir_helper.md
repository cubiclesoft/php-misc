DirHelper Class:  'support/dir_helper.php'
==========================================

The DirHelper class simplifies common file system tasks regarding directories, including recursive copy, delete, and permissions changes.  The primary purpose of this class is for building installers, live demos, and testing tools where files are being cloned out of an isolated archive or staging area and onto a functional system.

DirHelper::Delete($path, $recursive = true, $exclude = array())
---------------------------------------------------------------

Access:  public static

Parameters:

* $path - A string containing a path to delete.
* $recursive - A boolean that indicates whether to recursively delete or not (Default is true).
* $exclude - An array of exact match key-value pairs, where the keys are the filenames and values can be anything, that tell the function what files/directories to skip (Default is array()).

Returns:  Nothing.

This static function deletes the files and directories in the specified path, skipping any specified files/directories in the process.

DirHelper::Copy($srcdir, $destdir, $recurse = true, $exclude = array())
-----------------------------------------------------------------------

Access:  public static

Parameters:

* $srcdir - A string containing the path to copy.  No trailing slash, please.
* $destdir - A string containing the path to copy to.  If this directory does not exist, it will be created.  No trailing slash, please.
* $recurse - A boolean that indicates whether to recursively copy or not (Default is true).
* $exclude - An array of exact match key-value pairs, where the keys are the filenames and values can be anything, that tell the function what files/directories to skip (Default is array()).

Returns:  Nothing.

This static function copies the files and directories from the source directory to the destination directory, skipping any specified files/directories in the process.  The files that are copied are binary identical but their create/modify/access timestamps will be the current time and permissions will reflect the current effective user/group.

DirHelper::SetPermissions($path, $dirowner, $dirgroup, $dirperms, $fileowner, $filegroup, $fileperms, $recurse = true, $exclude = array())
------------------------------------------------------------------------------------------------------------------------------------------

Access:  public static

Parameters:

* $path - A string containing a path to alter.
* $dirowner - A boolean of false or a string containing the new owner of the directories in the path.  Only works on *NIX.
* $dirgroup - A boolean of false or a string containing the new group of the directories in the path.  Only works on *NIX.
* $dirperms - A boolean of false or an integer containing the new permissions of the directories in the path (e.g. 0750).
* $fileowner - A boolean of false or a string containing the new owner of the files in the path.  Only works on *NIX.
* $filegroup - A boolean of false or a string containing the new group of the files in the path.  Only works on *NIX.
* $fileperms - A boolean of false or an integer containing the new permissions of the files in the path (e.g. 0640).
* $recurse - A boolean that indicates whether to recursively copy or not (Default is true).
* $exclude - An array of exact match key-value pairs, where the keys are the filenames and values can be anything, that tell the function what files/directories to skip (Default is array()).

Returns:  Nothing.

This static function changes the permissions of the files and directories in the specified path, skipping any specified files/directories in the process.  This is an effective way to update permissions for whole directories of files at once.
