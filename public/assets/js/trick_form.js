$(document).ready(function() {
    // Event Listener onclick to the add-video button
    $('button.add-video').on('click', () => {
        // create a new input type file for medias[]
        $('.videos').append(`
        <div class="d-flex justify-content-between">
            <input type="text" name="videos[]" class="form-control mt-2">
            <button type="button" class="btn btn-outline-danger btn-sm remove-video m-2">Retirer</button>
        </div>`);
    });

    // Event Listener onclick to the add-image button
    $('button.add-image').on('click', () => {
        // create a new input type file for medias[]
        $('.images').append(`
        <div class="d-flex justify-content-between">
            <input type="file" name="images[]" class="form-control mt-2" accept="image/*">
            <button type="button" class="btn btn-outline-danger btn-sm remove-image m-2">Retirer</button>
        </div>`);
    });

    // Remove the added video input
    $('.videos').on('click', '.remove-video', function() {
        $(this).closest('.d-flex').remove();
    });

    // Remove the added image input
    $('.images').on('click', '.remove-image', function() {
        $(this).closest('.d-flex').remove();
    });
});