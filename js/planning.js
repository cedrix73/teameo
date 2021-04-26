/* ----------------------------------------------------------------------------
   ----------------------------------------------------------------------------
   DESCRIPTION :                                                         
 * Bibliothèque javascript pour le formulaire principal de team planning  
 * 
   ----------------------------------------------------------------------------
 * @author : Cédric Von Felten
 * @since  : 21/07/2015
 * @version : 1.0
   --------------------------------------------------------------------------*/


function cacherComposantsInfo() {
    $('#div_saisie_activite').hide();
    $("#message").hide();
    $("#img_loading").hide();
}

/**
 * initialiserFormulaire
 * Initie le datePicker et son comportement (affichage, sélection d'une date)
 * @returns néant
 */   
function initialiserFormulaire(){
    
    // initialisation calendriers
        this.datecal = "";
        $("#div_date").datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            firstDay: 1, 
            dateFormat: 'dd/mm/yy',
            altField: "#datepicker",
            closeText: 'Fermer',
            prevText: 'Précédent',
            nextText: 'Suivant',
            currentText: 'Aujourd\'hui',
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
            weekHeader: 'Sem.',
            onSelect: function (dateText, inst) {
                initialiserFormulaire.datecal = dateText;
                refreshCalendar(dateText);
            }
        });
        // Encodage UTF8 en ISO8591-1 
        //$('#lst_periodes').html(utf8_decode($('#lst_periodes').html()))
        
    
    initialiserFormulaire.datecal = $.datepicker.formatDate("dd/mm/yy", new Date());
    cacherComposantsInfo();
    // site
    str_site = $('#cbo_sites').val();
    // departement
    str_departement = $('#cbo_departements').val();
    // service
    //liste_services_load(str_site, str_departement); 
    refreshCalendar(initialiserFormulaire.datecal);
    
}


function refreshCalendar(dateText=null){
    if(dateText === null){
        dateText = initialiserFormulaire.datecal;
    }
    str_date = dateText;
    
    // Sites
    str_site = $('#cbo_sites').val();
    if(str_site == "Tous *" || str_site == " "){
        str_site="";
    }
    // Departements
    str_departement = $('#cbo_departements').val();
    if(str_departement == "Tous *" || str_departement == " "){
        str_departement="";
    }
    // Services
    str_services = $('#cbo_services').val();
    if(str_services == "Tous *" || str_services == " "){
        str_services="";
    }
    
    // largeur cadre min
    var l_planning = 1100; 
    var nb_col_sup = 0;
    // redimensionnement horizontal: à implémenter
    l_fenetre = $(window).width(); 
    nb_col_sup = parseInt((l_fenetre -1100) / 293);
    $("#img_loading").show();
    // requête ajax
    $.post("ajax/afficherCal.php", 
        {date_sel: str_date, 
         site_sel: str_site,
         departement_sel: str_departement, 
         service_sel: str_services,
         col_sup: nb_col_sup}, 
         function(data){
            $("#img_loading").hide();
            if(data.length >0) {
                $('#planning').html(data);
                redim();
                // redimensionnement horizontal 
                // On utilise des valeurs précalculées à partoir de lireDimensions()
                var largeur_col = 293;
                var l_defilement = parseInt(232 + (parseInt(nb_col_sup + 2) * largeur_col)  + 32);//1100
                var l_cadre = 250 + l_defilement;
                //alert(l_cadre);//1100
                //l_fenetre = $(window).width() - 50; 
                var delta = parseInt(l_fenetre -l_cadre);
                nb_col_sup = parseInt(delta / largeur_col);
                //alert(l_fenetre);
                if(l_cadre>=1100){
                    $('#defilement').css("width" , l_defilement + "px");
                    $('.col_droite').css("width" , parseInt(l_defilement) + "px");
                    $('#cadre').css("width" , l_cadre + "px");
                }
            }
            // recentrage du cadre principal
            $('#cadre').css('margin-left', parseInt(delta/2));
    });
  
}



function setDateWidget(dateRetournee){
    $('#div_date').datepicker({dateFormat: "dd/mm/yy"}).
        datepicker("setDate", dateRetournee);
}
  


/**
 * afficherSaisie
 * @param {type} date
 * @param {type} ressource_id
 * @param {type} numActivite (=0 pour tout jour travaillé)
 * @returns {undefined}
 */
function afficherSaisie(date, ressource_id, numActivite){
    $("#div_saisie_activite").html(initialiserFormulaire.saisieActivite);
    // patch 1.1.1 met à jour la liste des activités
    $.post("ajax/getActivites.php", 
        {
            id_activite:  numActivite
        },  
        function(data){
                if(data.length >0) {
                    $('#lst_activites').html(data);
                }
            }
        );
    
    $( "#supprimer" ).hide();
    $("#message").hide();
    $("#div_saisie_activite").slideDown();
    $("#txt_str_date_debut").val(date);
    $("#txt_str_date_fin").val(date);
    infoRessource.id = ressource_id;
    infoRessource.action = "insertion";
    $("#btn_valider_saisie").val("Valider");
    if(numActivite > 0){
        infoRessource.action = "modification";
        $("#btn_valider_saisie").val("Modifier");
        $( "#supprimer" ).show();
    }
   
   // jour choisi = jour min dans datePicker
   var datePat = /^(\d{1,2})(\/)(\d{1,2})(\/)(\d{4})$/;
   var matchArray = date.match(datePat);
   jour = matchArray[1];
   mois = parseInt(matchArray[3]-1);
   annee = parseInt(matchArray[5]);
   $(".champ_date").datepicker({
       minDate: new Date(annee, mois, jour),
       dateFormat: 'dd/mm/yy',
       firstDay: 1,
       monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
       dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],});
}

function supprimerSaisie(){
    infoRessource.action = "suppression";
    validerSaisie();
    //refreshCalendar($("#txt_str_date_debut").val());
}

function validerSaisie(){
    var fichierAjax = "insererEvent.php";
    if(infoRessource.action == "suppression"){
        fichierAjax = "supprimerEvent.php";
    }
    message = '';
    var date_debut = $("#txt_str_date_debut").val();
    var date_fin = $("#txt_str_date_fin").val();
    if((valid_DatePourComparaison(date_debut) > valid_DatePourComparaison(date_fin))){
        message = "La date de début doit être égale ou antérieure à la date de fin.";
        afficherMessage(message);
    }else{
        $("#img_loading").show();
        $.post("ajax/" + fichierAjax, {
            action_user: infoRessource.action,
            date_debut: ""+date_debut+"", 
            date_fin: ""+$("#txt_str_date_fin").val()+"", 
            ressource_id: ""+infoRessource.id+"", 
            activite_sel: ""+$("#lst_activites").val()+"", 
            periode_sel: ""+$("#lst_periodes").val()+""}, 
            function(data){
                $("#img_loading").hide();
                $( "#supprimer" ).html("&nbsp;");
                if(data.length >0) {
                    $("#div_saisie_activite").slideUp(2000).delay( 2000 ).fadeOut( 1000 );
                    refreshCalendar(initialiserFormulaire.datecal);
                    afficherMessage(data);
                    //message = data;
                    
                }
            }       
        );
    }
}

function liste_activites_load(){
    $.ajax({
        type: "POST",
        url: "ajax/liste_activites_load.php",
        datatype: "json",
        success: function(data)
        {
                if(ctle_erreur(data)){
                   var tab_elems = [];
                   var str_feedback = jQuery.parseJSON(data);
                    $.each(str_feedback, function(cle, valeur) {
                            tab_elems.push('<option value="' + valeur + '">' + valeur + '</option>');
                    });
                    $("#cbo_activites").html(tab_elems.join(''));
                }
        }
    });
}


function lireDimensions(){
    var cadre_margin = convertPxToInt($("#cadre").css("margin-left"));
    var cadre_pad = convertPxToInt($("#cadre").css("padding-left"));
    var espace_cadre = cadre_margin + cadre_pad;

    var largeur_menu= convertPxToInt($("#menu_gauche").css("width"));
    var largeur_menu_pad_gauche = convertPxToInt($("#menu_gauche").css("padding-left"));
    var largeur_menu_pad_droite = convertPxToInt($("#menu_gauche").css("padding-left"));
    var largeur_menu_tot = largeur_menu + largeur_menu_pad_gauche + largeur_menu_pad_droite;

    var largeur_legende = convertPxToInt($(".legende_ressources").css("width"));
    var largeur_legende_pad = convertPxToInt($(".legende_ressources").css("padding-left"));
    var largeur_legende_ress = largeur_legende + largeur_legende_pad;//232
    //alert(espace_cadre + largeur_menu_tot);//250

    var largeur_col = convertPxToInt($(".entete_semaine").css("width"));//293
    var l_defilement = parseInt(largeur_legende_ress + (parseInt(nb_col_sup + 2) * largeur_col)  + 32);//1100
    var l_cadre = espace_cadre + largeur_menu_tot + l_defilement;
    
    nb_col_sup = parseInt((l_fenetre -l_cadre) / largeur_col);
    //alert(l_fenetre);
    if(l_cadre>=1100){
        $('#defilement').css("width" , l_defilement + "px");
        $('.col_droite').css("width" , parseInt(l_defilement) + "px");
        $('#cadre').css("width" , l_cadre + "px");
    }
    $('#planning').css("height", "400px");
}



