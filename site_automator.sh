#!/bin/bash

##################################
# Nano Site Automator script
# This is a dummy script that can be ran from the command line
##################################


##################################
# begin FUNCTIONS


validate_command_tech_siteid()
{
	# Ensure that we have a valid command
	commands=('mkdir' 'chown' 'chown2' 'cp' 'mv' 'cleanup')
	if [[ -z $1 || ! " ${commands[@]} " =~ $1 ]]; then
		echo "Invalid command - $1"
		exit 1
	fi
	# Ensure that we have a valid tech
	trancos_techs=$(ls --ignore={www-*,cvs,schopr,rackconnect} /home)
	trancos_techs+=('daemon')
	if [[ -z $2 || ! " ${trancos_techs[@]} " =~ $2 ]]; then
		echo "Invalid tech - $2"
		exit 1
	fi
	# Check to make sure site_id is alphanumeric
	if [[ ! $3 =~ ^[a-z0-9]+$ ]]; then
		echo "Invalid site_id - $3"
		exit 1
	fi
}

# Check to make sure target folder exists in the site_templates directory
validate_template()
{
	if [[ -z $1 || -z $2 ]]; then
		echo "Invalid parameters in validate_template($1,$2)"
		exit 1
	fi
	templates=$(ls /home/$1/public_html/site_templates/)
	if [[ ! " ${templates[@]} " =~ $2 ]]; then
		echo "Invalid template - $2"
		exit 1
	fi
}

# Check to make sure a specific file exists, error out if it doesnt
validate_file()
{
	if [[ -z $1 || -z $2 || -z $3 ]]; then
		echo "Invalid parameters in validate_file($1,$2,$3)"
		exit 1
	fi
	files=$(ls -a /home/$1/public_html/nano/$2/)
	if [[ ! " ${files[@]} " =~ $3 ]]; then
		echo "Invalid file - $3"
		exit 1
	fi
}

# Throw an error if we try to clean up a directory that is already in CVS
validate_cleanup()
{
	if [[ -z $1 || -z $2 ]];then
		echo "Invalid parameters in validate_cleanup($1,$2)"
		exit 1
	fi
	files=$(ls /home/$1/public_html/nano/$2/)
	if [[ " ${files[@]} " =~ 'CVS' ]]; then
		echo "Cleanup Failed - CVS Exists in target directory"
		exit 1
	fi
}

# end FUNCTIONS
##################################
# begin MAIN

# The first argument will tell us which command to run
validate_command_tech_siteid $1 $2 $3

# This is the mkdir command
# It requires 2 additional arguments, trancos_tech and site_id
# This will create a new folder on the staging environment, owner of the folder will be 'daemon'
if [[ $1 == 'mkdir' ]]; then
	mkdir /home/$2/public_html/nano/$3/

# This is the chown command
# It requires 2 additional arguments, trancos_tech and site_id
# This will make sure that the owner of the folder is a trancos_tech and not 'daemon'
elif [[ $1 == 'chown' ]]; then
	chown -R $2:$2 /home/$2/public_html/nano/$3/

# This is the chown command
# It requires 2 additional arguments, trancos_tech and site_id
elif [[ $1 == 'chown2' ]]; then
	chown -R daemon:daemon /home/$2/public_html/nano/$3/

# This is the cp command
# It requires 3 additional arguments, trancos_tech, site_id and template
# This will copy all of the files from a template folder into the new location
elif [[ $1 == 'cp' ]]; then
	validate_template $2 $4
	cp -R /home/$2/public_html/site_templates/$4/* /home/$2/public_html/nano/$3/

# This is the mv command
# It requires 4 additional arguments, trancos_tech, site_id, oldname and newname
# This will attempt to rename a file by moving it to a different filename
# Ensure oldname exists before moving
elif [[ $1 == 'mv' ]]; then
	validate_file $2 $3 $4
	mv /home/$2/public_html/nano/$3/$4 /home/$2/public_html/nano/$3/$5

# This is the cleanup command
# It requires 2 additional arguments, trancos_tech and site_id
# This will attempt to clear out the directory and remove the folder
# Will not remove anything if folder 'CVS' exists in target directory
# Can we add any other checks to prevent malicious behavior on this?
elif [[ $1 == 'cleanup' ]]; then
	validate_cleanup $2 $3
	rm -rf /home/$2/public_html/nano/$3/
fi

exit 0

# end MAIN
##################################