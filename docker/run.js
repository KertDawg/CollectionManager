const fs = require("fs");
const { exec } = require("child_process");
const async = require("async");
const Configuration = require("./Configuration");
const { DockerServer } = require("./Configuration");



function SCP(SourcePath, DestinationPath, callback)
{
    exec("scp '" + SourcePath + "' '" + Configuration.DockerServer + ":" + Configuration.DockerPath + "/" + DestinationPath + "'", (error, stdout, stderr) => {
        if (error)
        {
            console.log("Error: " + error);
            console.log(stderr);
            callback();
        }
        else
        {
            console.log(stdout);
            console.log("File sent: " + SourcePath);
            callback();
        }
    });
}

function SSHCommand(WorkingDirectory, Command, callback)
{
    exec("ssh " + DockerServer + " 'cd /d " + WorkingDirectory + "&&" + Command + "'", (error, stdout, stderr) => {
        if (error)
        {
            console.log("Error: " + error);
            console.log(stderr);
            callback();
        }
        else
        {
            console.log(stdout);
            console.log("Executed command.");
            callback();
        }
    });
}


SSHCommand(Configuration.DockerPath, "SET DOCKER_BUILDKIT=0&& SET COMPOSE_DOCKER_CLI_BUILD=0&&docker run -p 80:80 -d kertdawg/collectionmanager .", () => { console.log("Done."); });

