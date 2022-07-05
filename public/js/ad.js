
$('#add-image').click(function(){

    // Récupérer le numéro des futurs champs

    const index = +$('#widget-count').val() // le plus sert à renvoyer un int

    // récupérer le prototype des entrées

    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g,index)
    //console.log(tmpl);

    // Injecter le code dans la div

    $('#annonce_images').append(tmpl);

    // On ajoute 1 à la valeur initiale de la collection

    $('#widget-count').val(index+1);
    
    deleteButtons();



});

function updateCounter(){

    const count = +$('#annonce_images div.form-group').length;

    // On met à jour la valeur de widget-count

    $('widget-count').val(count);
}

function deleteButtons(){
    $('button[data-action = "delete"]').click(function(){
        const target = this.dataset.target;

        $(target).remove();
    });
}

updateCounter();
deleteButtons();

