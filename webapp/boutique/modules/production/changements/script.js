$(function(){


    $("tr.fini").hide()

    $("input[type=checkbox].onoffswitch-checkbox").change(function(event) {
        if($(this).is(":checked")){
            Loader.start()
            setTimeout(function(){
                Loader.stop()
                $("tr.fini").fadeIn(400)
            }, 500);
        }else{
            $("tr.fini").fadeOut(400)
        }
    });

    $("#top-search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table.table-mise tr:not(.no)").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });



    
    annulerTransfert = function(id){
        alerty.confirm("Voulez-vous vraiment annuler cette mise en boutique ?", {
            title: "Annuler la mise en boutique",
            cancelLabel : "Non",
            okLabel : "OUI, annuler",
        }, function(){
            var url = "../../webapp/boutique/modules/production/transfertstock/ajax.php";
            alerty.prompt("Entrer votre mot de passe pour confirmer l'opération !", {
                title: 'Récupération du mot de passe !',
                inputType : "password",
                cancelLabel : "Annuler",
                okLabel : "Valider"
            }, function(password){
                Loader.start();
                $.post(url, {action:"annulerTransfert", id:id, password:password}, (data)=>{
                    if (data.status) {
                        window.location.reload()
                    }else{
                        Alerter.error('Erreur !', data.message);
                    }
                },"json");
            })
        })
    }


    $("select[name=produit_id_destination]").change(function(){
        var url = "../../webapp/boutique/modules/production/changements/ajax.php";
        id = $(this).val();
        $.post(url, {action:"getEmballageDestination", id:id}, (data)=>{
            $("div.div-destination").html(data);
        },"html");
    })

    $("body").on("click", ".emballage-destination", function(){
        $(".emballage-destination").removeClass("selected")
        $(this).addClass("selected")
        session("emballagedestination_id", $(this).attr("id"))
    })




    $("select[name=produit_id_source]").change(function(){
        var url = "../../webapp/boutique/modules/production/changements/ajax.php";
        id = $(this).val();
        $.post(url, {action:"getEmballageSource", id:id}, (data)=>{
            $("div.div-source").html(data);
        },"html");
    })

    $("body").on("click", ".emballage-source", function(){
        $(".emballage-source").removeClass("selected")
        $(this).addClass("selected")
        session("emballagesource_id", $(this).attr("id"))
    })
})