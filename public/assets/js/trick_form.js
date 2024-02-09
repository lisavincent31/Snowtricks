$(document).ready(function() {
    // Event Listener onclick to the add-media button
    $('button.add-media').on('click', () => {
        // create a new input type file for medias[]
        $('#medias').append(`
        <div class="d-flex justify-content-between">
            <input type="file" name="medias[]" class="form-control" accept="image/*,video/*">
            <button type="button" class="btn btn-white remove-media">Supprimer cette image</button>
        </div>`);
    });

     // Add click event listener to dynamically remove an input for medias[]
     $('#medias').on('click', '.remove-media', function() {
        $(this).closest('.d-flex').remove();
    });
});