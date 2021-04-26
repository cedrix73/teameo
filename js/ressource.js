/* ----------------------------------------------------------------------------
   ----------------------------------------------------------------------------
   DESCRIPTION :                                                         
 * Bibliothèque javascript pour le formulaire principal de team planning  
 * 
   ----------------------------------------------------------------------------
 * @author : Cédric Von Felten
 * @since  : 28/10/2014
 * @version : 1.3
   --------------------------------------------------------------------------*/


   function afficherFormRessources(){
    if( $("#affichage_activite").val() == 'form_ressources'){
        $("#div_saisie_activite").toggle();
        $("#affichage_activite").val("");
    } else {
        $.post("ajax/afficherFormRessources.php", 
             function(data){
                if(data.length >0) {
                    $('#div_saisie_activite').html(data);
                    $("#div_saisie_activite").slideDown();
                }
        });
        $("#affichage_activite").val("form_ressources");
    }
}

function form_departements_load(site_sel){
    $.ajax({
        type: "post",
        url: "ajax/listeDepartementsLoad.php",
        data: "site=" + site_sel,
        data: {"site_sel": site_sel, "contexte_insertion": true},
        datatype: "json",
        success: function(data)
        {
            var tab_elems = [];
            var str_feedback = jQuery.parseJSON(data);
            $.each(str_feedback, function(cle, valeur) {
                tab_elems.push('<option value="' + cle + '">' + valeur + '</option>');
            });
            $("#res_departement").html(tab_elems.join(''));
            form_services_load(site_sel, $("#res_departement").val());


        },
        error: function(message)
        {
            afficherMessage(message);
        }
    });
}

function form_services_load(site_sel, departement_sel){
    $.ajax({
        type: "POST",
        url: "ajax/listeServicesLoad.php",
        data: {"site_sel": site_sel, "departement_sel":departement_sel, "contexte_insertion": true},
        datatype: "json",
        success: function(data)
        {
            var tab_elems = [];
            var str_feedback = jQuery.parseJSON(data);
            $.each(str_feedback, function(cle, valeur) {
                tab_elems.push('<option value="' + cle + '">' + valeur + '</option>');
            });
             $("#res_service").html(tab_elems.join(''));

        }
    });
}

function infoRessource(nom, prenom){
    infoRessource.nom = replaceBlancs(nom);
    infoRessource.prenom = replaceBlancs(prenom);
    $("#lgd_saisie_activite").text("saisie d'activité de <i>" + infoRessource.prenom + " " + infoRessource.nom + "</i>");
}

function validerSaisieRessource(){
    var json_string = validerSaisieForm("panel_ressource");
    if(json_string !== false && json_string !==undefined){
        $("#img_loading").show();
        $.post("ajax/insererRessource.php", {
            json_datas: json_string}, 
            function(data){
                $("#img_loading").hide();
                if(data.length >0) {
                    if(data.substr(0, 7) !== 'Erreur:') {
                        $("#div_saisie_activite").slideUp(2000).delay( 2000 ).fadeOut( 1000 );
                        initialiserFormulaire();
                    }
                    afficherMessage(data);
                }
            }       
        );
    }
}



