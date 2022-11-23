const fs = require("fs");
const archiver = require("archiver");
const { exec } = require("child_process");
const format = require("date-format");


console.log("Running SPA build...");

exec("quasar build", { cwd: __dirname + "/../client" }, function (error)
{
    if (error)
    {
        throw error;
    }

    //  Create the output folder.
    const dir = __dirname + "/..";


    //  Set up zip file.
    console.log("Setting up zip file...");
    var output = fs.createWriteStream(dir + "/deploy_" + format("yyyy-MM-dd-hh-mm-ss") + ".zip");
    var archive = archiver("zip", {
        zlib: { level: 9 }
    });

    archive.pipe(output);
    output.on("end", function ()
    {
        console.log("Done.");
    });

    console.log("Queueing up files...");

    //  Add files.
    archive.file("../server/index.php", { name: "api/index.php" });
    archive.file("../server/generate-app-version-guid.php", { name: "api/generate-app-version-guid.php" });
    archive.file("../server/.htaccess", { name: "api/.htaccess" });
    archive.directory("../server/utils", "api/utils");
    archive.directory("../server/keys", "api/keys");
    archive.directory("../server/controllers", "api/controllers");
    archive.directory("../server/vendor", "api/vendor");
    archive.directory("../client/dist/spa/", false);


    //  Write the zip file.
    console.log("Writing zip file...");
    archive.finalize();

    //  The end event will fire at this point, ending the program.
});


