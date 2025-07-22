var caching_startupStatus = function(colNumber, row){
    var col = $('td:eq('+colNumber+')', row),
        colvar = col.text();
    if (colvar == "FAILED"){
        colvar = i18n.t('failed')
    }
    col.text(colvar)
}

var caching_regStatus = function(colNumber, row){
    var col = $('td:eq('+colNumber+')', row),
        colvar = col.text();
    if (colvar == "1"){
        colvar = i18n.t('caching.registered')
    } else if (colvar == "0"){
        colvar = i18n.t('caching.not_registered')
    } else if (colvar == "-1"){
        colvar = i18n.t('error')
    } else {
        colvar = colvar
    }
    col.text(colvar)
}

var caching_fileSize = function(colNumber, row){
    var col = $('td:eq('+colNumber+')', row),
        colvar = col.text();
    col.text(fileSize(parseInt(colvar), 2))
}