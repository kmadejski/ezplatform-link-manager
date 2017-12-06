(function(doc) {
    const statusField = doc.getElementById('status');

    statusField.addEventListener('change', function() {
        this.form.submit();
    });
}) (document);
