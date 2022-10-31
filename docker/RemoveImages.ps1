#  This script removes old project images and images with name <none> from Docker Desktop.



$StaleImages = docker images -q kertdawg/collectionmanager

ForEach ($Image in $StaleImages)
{
    docker rmi $Image -f
}


$StaleImages = docker images -f "dangling=true" -q

ForEach ($Image in $StaleImages)
{
    docker rmi $Image -f
}
