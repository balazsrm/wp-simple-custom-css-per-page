jQuery(document).ready(function($) {
    // Append custom CSS field to Quick Edit row dynamically
    $(document).on('click', '.editinline', function() {
        var thisRow = $(this).closest('tr');
        var editRow = thisRow.siblings('.inline-edit-row').first();
        var value = thisRow.find('.ccp-custom-css-data').text();

        var customCssField = '<label class="inline-edit-custom-css">';
        customCssField += '<span class="title">Custom CSS</span>';
        customCssField += '<textarea name="ccp_custom_css" rows="2" cols="22">' + value + '</textarea>';
        customCssField += '</label>';

        // Check if the field is already added to avoid duplicates
        if (!editRow.find('.inline-edit-custom-css').length) {
            editRow.find('.inline-edit-col-left').append(customCssField);
        }

        // Add nonce field
        var nonceField = '<input type="hidden" name="ccp_custom_css_nonce" value="' + CCP_CustomCSS.nonce + '" />';
        editRow.find('.inline-edit-col-left').append(nonceField);
    });
});
