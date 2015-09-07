# how

Note that this documentation is also available on http://how.nl5.de.
For more information, see also http://www.nicolailang.de.

how is a simple python script I wrote to increase my "command-line agility".
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
how to add a user in ubuntu
or less grammatically correct
how add user ubuntu

At this point is should be clear why the script is named "how".
The response to the previous question reads:
Add a user and create its home directory automatically: 
- sudo adduser "username" 

Add a user without a home directory: 
- sudo useradd "username" 
- sudo passwd "username"


