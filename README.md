# how - a command-line Q&A tool

Note that this documentation is also available at http://how.nl5.de.

**how** is a simple Python script I wrote to increase my "command-line agility".

It provides quick access to code snippets that I use too rarely to remember, but frequently enough to annoy me whenever I have to look them up anew.

Creating the script was motivated by the following situation that disrupts my actual workflow quite frequently: 

  1. Work productively and happily, with your hands on the keyboard.
  2. You encounter one of those non-routine tasks. What was that sequence of options again to do this and that?
  3. Grab the mouse, open Google, enter keywords, press enter.
  4. Hurry up! Hit the first result, end up on Stack Exchange.
  5. Copy the code snipped you used several times before with the options you always forget.
  6. Proceed with your work.

Most people working with the command-line may have encountered this kind of workflow disruption, though the list of such disruptive, "non-routine" snippets clearly depends on the user and its command-line affinity.

how was written to minimize such disruptions. It is used as follows:
```
how to add a user in ubuntu
```
or less grammatically correct
```
how add user ubuntu
```

At this point is should be clear why the script is named "how".

The response to the previous question reads:
```
Add a user and create its home directory automatically: 
- sudo adduser "username" 

Add a user without a home directory: 
- sudo useradd "username" 
- sudo passwd "username"
```
Now you can proceed without leaving the command-line at any point, provided your database contains the required code-snipped.

## Installation

**how** is a simple, single-file Python script. It requires Python 3 (tested with Python 3.4) and the following Python packages (install them with your OS package manager or via `pip`):

- `termcolor`
- `xml`
- `configparser`
- `urllib`

Then download the latest version via git
```
git clone http://github.com/NcLang/how
```
Place the script in a directory of your choice (e.g. `~/bin/` or `~/scripts/`), make sure that it is executable,
```
chmod 755 how.py
```
and place a link in your system's search path,
```
sudo ln -s /path/to/script/how.py /usr/bin/how
```
It is recommended to call the script "how" for obvious reasons.

Now you are ready to go.

## Configuration

When you first call how, it will create a configuration file in `~/.how/` and download the default list from `how.nl5.de` as XML file into the same directory. The script uses this file as database, so no internet connection is required once the list has been downloaded.

It will, however, update the list automatically whenever you call how and the local file is older than the update interval specified as `UpdateInterval` in the configuration file `how.cfg`. You can change the list used to search for code snippets by setting the parameter `List` to one of the names listed below in the API section.

To force an update and redownload the currently specified list, call
```
how -u
```
If you want to manage your database (`~/.how/howdb.xml`) manually (e.g. to add/modify your personal entries), you can disable the automatic update (which would overwrite your local modifications) by setting `UpdateInterval = 0`.

## API

### Using the API

The lists containing regular expression defining the questions and the corresponding answers are internally stored in a MySQL database on my webserver and served as dynamically generated XML files.

The latter can be directly accessed publicly via
```
http://how.nl5.de/xml/LIST
```
where `LIST` should be replaced by one of the valid list names listed below.

The above URL ist set as default API in the python script.

You can define the list your local script uses by setting the `List` parameter in the configuration file `~/.how/how.cfg`. By default the list "QA-default" is used.

### Setting up your own Q&A API server

The Q&A lists are internally hosted by a MySQL database and served as XML files. 
To set up your own API server, you need
- a webserver (Apache,nginx),
- PHP
- and a MySQL server accessible via PHP.

First, download the `api` folder to your webserver so that the `index.php` can be accessed via HTTP.

Then create a new MySQL database with dedicated user/password and insert this information at the right places in `config.php`. 

If you navigate to the `index.php`, it will list all available tables in your database as possible Q&A lists.
It is recommended to manage your tables by a MySQL frontend such as PHP-MyAdmin.

To set up your first Q&A list, run the following SQL commands,
```
CREATE TABLE IF NOT EXISTS `LISTNAME` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL,
  `answer` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
where `LISTNAME` is to be replaced by the name of your Q&A list.

You can now start filling your table with code snippets. 
For each snippet, add a row to your MySQL table (Q&A list) with the following data:

1. `question`-row: The question is captured by a regular expression of the form `* ... $`. Try to contrive regular expressions that are both unique for the code snipped and as flexible as possible to capture different forms of the same question.
2. `answer`-row: Add a short description and one or more code snippets. Each snippet should start with `- ` in a new line (this highlights the snipped in the command-line).

### Publicly available Q&A lists

The following Q&A lists are currently available (click to view the XLS-styled XML files):

- [QA-default](http://how.nl5.de/xml/QA-default) @ [how.nl5.de](http://how.nl5.de)

In case you set up an API server and compiled a useful Q&A list on your own, and are willing to make this list publicly available, just drop me a line so I can add a link to your API server above.
