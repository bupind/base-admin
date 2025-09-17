<script>
    document.querySelectorAll('.{{ $class }}').forEach(checkbox => {
        checkbox.addEventListener('change', function(target) {

            var key = this.dataset.key;
            var value = this.checked ? 'on' : 'off';
            var url = '{{ $resource }}/' + key;
            var obj = {
                method: 'post',
                data: {
                    _method: 'PUT',
                    '{{ $name }}': value,
                    'after-save': 'exit'
                }
            }

            backend.ajax.request(url, obj, function(result) {
                if (result.status) {
                    backend.toastr.success(result.data);
                } else {
                    backend.toastr.warning(result.data);
                }
            });
        })
    });
</script>
