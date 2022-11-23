#!/bin/sh

#  Run this from a dev server to build, upload, and deploy the app.


#  Remove old archives.
echo Removing old archives...
rm deploy*.zip

echo Building the archive...
#  Build the archive.
cd scripts
npm run package
cd ..

echo Uploading the archive...
#  Upload the archive.
scp -i ~/.ssh/PBWDev deploy*.zip kertdawg@www.kertdawg.net:public_html/collections

echo Executing deployment script...
#  Execute the deployment script.
ssh -i ~/.ssh/PBWDev kertdawg@www.kertdawg.net "cd public_html/collections; ./Deploy.sh"

#  Done.
echo Done with upload.
