$(document).ready(function() { 
    $("table").tablesorter({
        debug: false,
        headers: { 
            // assign the secound column (we start counting zero) 
            2: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            },
        }
    });
});
