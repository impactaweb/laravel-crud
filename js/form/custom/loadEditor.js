module.exports = (function($, axios){
    $(document).ready(function(){
        $summernote = $('[data-type="summernote"]')

        if(!$summernote[0]) return

        $summernote.summernote({
            lang: 'pt-BR',
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['picture', 'table', 'link', 'video']],
                ['misc', ['fullscreen', 'help']]
            ],
            codeviewFilter: false,
            codeviewIframeFilter: true,
            // Enviando em base64, caso queira sar a forma do v1 descomente esta a parte e adcione a rotta de upload
            // callbacks: {
            //     onImageUpload: function(files) {
            //         const form = new FormData()
            //         const config = {
            //             headers: {'content-type': 'multipart/form-data'}
            //         }

            //         Array.from(files).forEach(function(file, idx){
            //             form.append('media' + idx, file, file.name)
            //         })

            //         axios.post(END_POINT, form, config)
            //         .then(function(res) {

            //             console.log(res)
            //             return
            //             const imgUrl = res.data.imgUrl
            //             const img = document.createElement('img')
            //             img.src = imgUrl
            //             $summernote.summernote('insertNode', img)
            //         })
            //         .catch(function(err){
            //             alert('falha no upload da imagem, verifique a extensão da imagem')
            //         })
            //     }
            // }
        })
    })
})(window.jQuery, window.axios)

