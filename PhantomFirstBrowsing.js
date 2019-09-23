var webPage = require('webpage');
var page = webPage.create();
var fs = require('fs');

page.open('https://www.mtgstocks.com/interests', function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
            phantom.exit(1);
        } else {
            window.setTimeout(function () {
            var content = page.content;
 				    console.log('Content: ' + content);
  				  fs.write("file.txt", content)
            phantom.exit();
            }, 2000);
        }
    });