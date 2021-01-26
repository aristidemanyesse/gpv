$(function(){


 $("select[name=produit_id]").change(function(){
    var url = "../../webapp/entrepot/modules/production/reconditionnement/ajax.php";
    id = $(this).val();
    $.post(url, {action:"getEmballageSource", id:id}, (data)=>{
        $("div.div-source").html(data);
    },"html");
})

 $("body").on("click", ".emballage-source", function(){
    $(".emballage-source").removeClass("selected")
    $(this).addClass("selected")
    session("emballage_id", $(this).attr("id"))
})

})