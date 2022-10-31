#  This script removes old images from Docker Desktop.



$Containers = docker ps -a -q --filter ancestor=kertdawg/collectionmanager

ForEach ($Container in $Containers)
{
    $Stopped = docker stop $Container

    ForEach ($Remove in $Stopped)
    {
        docker rm $Remove
    }
}
