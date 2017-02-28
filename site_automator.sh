#!/bin/bash

##################################
# Nano Site Automator script
# This is a dummy script that can be ran from the command line
##################################


##################################
# begin FUNCTIONS

# Ensure that we have a valid command
validate_command()
{
	commands=('mkdir' 'chown' 'cp' 'find' 'mv' 'rm' 'cleanup')
	if [[ -z $1 || ! " ${commands[@]} " =~ $1 ]]; then
		echo 'Invalid command - '.$1
		exit 1
	fi
}

# Ensure that we have a valid tech
validate_tech()
{
	trancos_techs=$(ls --ignore={www-*,cvs,schopr,rackconnect} /home)
	if [[ -z $1 || ! " ${trancos_techs[@]} " =~ $1 ]]; then
		echo 'Invalid tech - '.$1
		exit 1
	fi
}

# Check to make sure site_id is alphanumeric
validate_siteid()
{
	if [[ ! $1 =~ ^[a-z0-9]+$ ]]; then
		echo 'Invalid site_id - '.$1
		exit 1
	fi
}

# Check to make sure target folder exists in the site_templates directory
validate_template()
{
	templates=$(ls /home/$1/public_html/site_templates/)
	if [[ -z $1 || -z $2 || ! " ${templates[@]} " =~ $2 ]]; then
		echo 'Invalid template - '.$1
		exit 1
	fi
}

# Check to make sure a specific file exists, error out if it doesnt
validate_file()
{
	files=$(ls /home/$1/public_html/nano/$2/)
	if [[ -z $1 || -z $2 || -z $3 || ! " ${files[@]} " =~ $3 ]]; then
		echo 'Invalid file - '.$1
		exit 1
	fi

}

# Throw an error if we try to clean up a directory that is already in CVS
validate_cleanup()
{
	files=$(ls /home/$1/public_html/nano/$2/)
	if [[ -z $1 || -z $2 || " ${files[@]} " =~ 'CVS' ]]; then
		echo 'Cleanup Failed - CVS Exists in target directory'
		exit 1
	fi
}

# end FUNCTIONS
##################################
# begin MAIN

# The first argument will tell us which command to run
validate_command $1

# This is the mkdir command
# It requires 2 additional arguments, trancos_tech and site_id
# This will create a new folder on the staging environment, owner of the folder will be 'daemon'
if [[ $1 == 'mkdir' ]]; then
	validate_tech $2
	validate_siteid $3
	mkdir /home/$2/public_html/nano/$3/

# This is the chown command
# It requires 2 additional arguments, trancos_tech and site_id
# This will make sure that the owner of the folder is a trancos_tech and not 'daemon'
elif [[ $1 == 'chown' ]]; then
	validate_tech $2
	validate_siteid $3
	chown -R $2:$2 /home/$2/public_html/nano/$3/

# This is the cp command
# It requires 3 additional arguments, trancos_tech, site_id and template
# This will copy all of the files from a template folder into the new location
elif [[ $1 == 'cp' ]]; then
	validate_tech $2
	validate_siteid $3
	validate_template $2 $4
	cp -R /home/$2/public_html/site_templates/$4/* /home/$2/public_html/nano/$3/

# This is the find command
# It requires 3 additional arguments, trancos_tech, site_id and variable_arr
# This will parse through the files finding one string and replacing it with another
elif [[ $1 == 'find' ]]; then
	validate_tech $2
	validate_siteid $3
	variable_arr=$4
	IFS=';' read -a variable_arr <<< "$variable_arr"
	find /home/$2/public_html/nano/$3/ -type f -exec sed -i "s|\^\^\^BASE_URL\^\^\^|${variable_arr[0]}|g; s|\^\^\^FBAPPID\^\^\^|${variable_arr[1]}|g; s|\^\^\^FBSECRET\^\^\^|${variable_arr[2]}|g; s|\^\^\^FBVERSION\^\^\^|${variable_arr[3]}|g; s|\^\^\^SITE_NAME\^\^\^|${variable_arr[4]}|g; s|\^\^\^SITE_ID\^\^\^|${variable_arr[5]}|g; s|\^\^\^MAIN_COLOR\^\^\^|${variable_arr[6]}|g; s|\^\^\^LOGO_NAME\^\^\^|${variable_arr[7]}|g; s|\^\^\^BACKGROUND_NAME\^\^\^|${variable_arr[8]}|g; s|\^\^\^FBPAGE\^\^\^|${variable_arr[9]}|g; s|\^\^\^ANALYTICS_ID\^\^\^|${variable_arr[10]}|g; s|\^\^\^KEYWORDS\^\^\^|${variable_arr[11]}|g; s|\^\^\^DESCRIPTION\^\^\^|${variable_arr[12]}|g; s|\^\^\^SITE_URL\^\^\^|${variable_arr[13]}|g; s|\^\^\^HTACCESS_PATH_LIVE\^\^\^|${variable_arr[0]}|g; s|\^\^\^HTACCESS_PATH_STAGING\^\^\^|/~$2/nano/${variable_arr[5]}/|g" {} +

# This is the mv command
# It requires 4 additional arguments, trancos_tech, site_id, oldname and newname
# This will attempt to rename a file by moving it to a different filename
# Ensure oldname exists before moving
elif [[ $1 == 'mv' ]]; then
	validate_tech $2
	validate_siteid $3
	validate_file $2 $3 $4
	mv /home/$2/public_html/nano/$3/$4 /home/$2/public_html/nano/$3/$5

# This is the rm command
# It requires 3 additional arguments, trancos_tech, site_id, and filename
# This will attempt to remove a single file
# Ensure filename exists before removing
elif [[ $1 == 'rm' ]]; then
	validate_tech $2
	validate_siteid $3
	validate_file $2 $3 $4
	rm /home/$2/public_html/nano/$3/$4

# This is the cleanup command
# It requires 2 additional arguments, trancos_tech and site_id
# This will attempt to clear out the directory and remove the folder
# Will not remove anything if folder 'CVS' exists in target directory
# Can we add any other checks to prevent malicious behavior on this?
elif [[ $1 == 'cleanup' ]]; then
	validate_tech $2
	validate_siteid $3
	validate_cleanup $2 $3
	rm -rf /home/$2/public_html/nano/$3/
fi

exit 0

# end MAIN
##################################