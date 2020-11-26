$(function(){


    $("#top-search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table.table-transfert tr:not(.no)").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });




    demandetransfertboutique = function(){
        var formdata = new FormData($("#formTransfertboutiquedemande")[0]);
        
        tableau = new Array();
        $("#modal-transfertboutique-demande tr input").each(function(index, el) {
            var id = $(this).attr('data-id');
            var format = $(this).attr('data-format');
            var val = $(this).val();
            if (val > 0) {
                var item = id+"-"+format+"-"+val;
                tableau.push(item);
            }       
        });
        formdata.append('listeproduits', tableau);

        alerty.confirm("Voulez-vous vraiment confirmer la demande de transfert en boutique de ces produits ?", {
            title: "Confirmation de la demande",
            cancelLabel : "Non",
            okLabel : "OUI, Valider",
        }, function(){
            Loader.start();
            var url = "../../webapp/boutique/modules/production/transfertboutique/ajax.php";
            formdata.append('action', "demandetransfertboutique");
            $.post({url:url, data:formdata, contentType:false, processData:false}, function(data){
                if (data.status) {
                    window.location.reload();
                }else{
                    Alerter.error('Erreur !', data.message);
                }
            }, 'json')
        })
    }


    accepter = function(id){
        alerty.confirm("Voulez-vous vraiment confirmer cette demande de transfert en boutique ?", {
            title: "Confirmation de la demande",
            cancelLabel : "Non",
            okLabel : "OUI, Accepter",
        }, function(){
            session("transfertboutique_id", id);
            modal("#modal-acceptertransfertboutique"+id);
        })
    }



    $("#formAccepterTransfertboutique").submit(function(event) {
        Loader.start();
        $(this).find("input.vendus").last().change();
        var url = "../../webapp/boutique/modules/production/transfertboutique/ajax.php";
        var formdata = new FormData($(this)[0]);
        var tableau = new Array();
        $(this).find("table tr input.recu").each(function(index, el) {
            var id = $(this).attr('data-id');
            
            var vendu = $(this).val();
            tableau.push(id+"-"+vendu);
        });
        formdata.append('tableau', tableau);

        formdata.append('action', "accepterTransfertboutique");
        $.post({url:url, data:formdata, contentType:false, processData:false}, function(data){
            if (data.status) {
                window.location.reload()
            }else{
                Alerter.error('Erreur !', data.message);
            }
        }, 'json');
        return false;
    });


///////////////////////////////////////////////////////////////////////////////////////////////////////////////


transfertboutique = function(){
    var formdata = new FormData($("#formTransfertboutique")[0]);
    tableau = new Array();
    $("#modal-transfertboutique tr input").each(function(index, el) {
        var id = $(this).attr('data-id');
        var format = $(this).attr('data-format');
        var val = $(this).val();
        if (val > 0) {
            var item = id+"-"+format+"-"+val;
            tableau.push(item);
        }      
    });
    formdata.append('listeproduits', tableau);

    alerty.confirm("Voulez-vous vraiment confirmer la demande de transfert en boutique de ces produits ?", {
        title: "Confirmation de la demande",
        cancelLabel : "Non",
        okLabel : "OUI, Valider",
    }, function(){
        Loader.start();
        var url = "../../webapp/boutique/modules/production/transfertboutique/ajax.php";
        formdata.append('action', "transfertboutique");
        $.post({url:url, data:formdata, contentType:false, processData:false}, function(data){
            if (data.status) {
                window.location.reload();
            }else{
                Alerter.error('Erreur !', data.message);
            }
        }, 'json')
    })
}



$("#formValiderTransfertboutique").submit(function(event) {
    Loader.start();
    $(this).find("input.vendus").last().change();
    var url = "../../webapp/boutique/modules/production/transfertboutique/ajax.php";
    var formdata = new FormData($(this)[0]);
    var tableau = new Array();
    $(this).find("table tr input.recu").each(function(index, el) {
        var id = $(this).attr('data-id');

        var vendu = $(this).val();
        tableau.push(id+"-"+vendu);
    });
    formdata.append('tableau', tableau);

    formdata.append('action', "validerTransfertboutique");
    $.post({url:url, data:formdata, contentType:false, processData:false}, function(data){
        if (data.status) {
            window.location.reload()
        }else{
            Alerter.error('Erreur !', data.message);
        }
    }, 'json');
    return false;
});



terminer = function(id){
    alerty.confirm("Voulez-vous vraiment confirmer cette transfert en boutique ?", {
        title: "Confirmer transfert en boutique",
        cancelLabel : "Non",
        okLabel : "OUI, terminer",
    }, function(){
        session("transfertboutique_id", id);
        modal("#modal-transfertboutique-"+id);
    })
}

})