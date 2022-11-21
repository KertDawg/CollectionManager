const fs = require("fs");
const { exec } = require("child_process");
const async = require("async");
const Configuration = require("./Configuration");
const { DockerServer } = require("./Configuration");


var Commands = [];


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


Commands.push((callback) => {
    //  Clean old artifacts.
    try
    {
        console.log("Deleting old client package...");
        fs.unlinkSync("web-client.tar");
    }
    catch (error)
    {
        //  The file probably doesn't exist.
    }

    callback();
});

Commands.push((callback) => {
    //  Clean old artifacts.
    try
    {
        console.log("Deleting old server package...");
        fs.unlinkSync("web-server.tar");
    }
    catch (error)
    {
        //  The file probably doesn't exist.
    }

    callback();
});

Commands.push((callback) => {
    console.log("Building client...");

    exec("quasar build", { cwd: "../client" }, (error, stdout, stderr) => {
        console.log("Web client built.");
        callback();
    });
});

Commands.push((callback) => {
    exec("tar -cf ../../docker/web-client.tar spa", { cwd: "../client/dist" }, (error, stdout, stderr) => {
        console.log("Web client packaged.");
        callback();
    });
});

Commands.push((callback) => {
    //  Build web server.
    exec("tar -cf docker/web-server.tar server", { cwd: ".." }, (error, stdout, stderr) => {
        console.log("Web server packaged.");
        callback();
    });
});

Commands.push((callback) => {
    SCP("./DockerFile", "Dockerfile", callback);
});

Commands.push((callback) => {
    SCP("./startup.sh", "startup.sh", callback);
});

Commands.push((callback) => {
    SCP("./RemoveContainers.ps1", "RemoveContainers.ps1", callback);
});

Commands.push((callback) => {
    SCP("./RemoveImages.ps1", "RemoveImages.ps1", callback);
});

Commands.push((callback) => {
    SCP("./50-server.cnf", "50-server.cnf", callback);
});

Commands.push((callback) => {
    SCP("../database/Accounts.sql", "Accounts.sql", callback);
});

Commands.push((callback) => {
    SCP("../database/Collections.sql", "Collections.sql", callback);
});

Commands.push((callback) => {
    SCP("../server/.htaccess", ".htaccess", callback);
});

Commands.push((callback) => {
    SCP("./web-client.tar", "web-client.tar", callback);
});

Commands.push((callback) => {
    SCP("./web-server.tar", "web-server.tar", callback);
});

Commands.push((callback) => {
    console.log("Removing old containers...");
    SSHCommand(Configuration.DockerPath, "powershell .\\RemoveContainers.ps1", callback);
});

Commands.push((callback) => {
    console.log("Removing old images...");
    SSHCommand(Configuration.DockerPath, "powershell .\\RemoveImages.ps1", callback);
});

Commands.push((callback) => {
    console.log("Deleting old client folder...");
    SSHCommand(Configuration.DockerPath, "DEL /S /Q web-client", callback);
});

Commands.push((callback) => {
    console.log("Deleting old server folder...");
    SSHCommand(Configuration.DockerPath, "DEL /S /Q web-server", callback);
});

Commands.push((callback) => {
    console.log("Extracting client...");
    SSHCommand(Configuration.DockerPath, "tar xf web-client.tar", callback);
});

Commands.push((callback) => {
    console.log("Extracting server...");
    SSHCommand(Configuration.DockerPath, "tar xf web-server.tar", callback);
});

Commands.push((callback) => {
    console.log("Building docker image...");
    SSHCommand(Configuration.DockerPath, "SET DOCKER_BUILDKIT=0&& SET COMPOSE_DOCKER_CLI_BUILD=0&& docker build -t kertdawg/collectionmanager .", callback);
});


async.waterfall(Commands).then(() => { console.log("Done."); });

