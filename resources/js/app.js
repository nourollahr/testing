require('./bootstrap');

import tinyDatePicker from 'tiny-date-picker';
tinyDatePicker({ input: document.querySelector('input.tiny-date-picker') });

let postImageInput = document.getElementById('postImageInput');

postImageInput.addEventListener('change', (e) => {
    let image = e.target.files[0],
        data = new FormData();

    data.append('image', image);

    axios.post('/admin/upload', data)
        .then(response => {
            let url = response.data.url;

            document.querySelector('input[name="image"][type="hidden"]').value = url;
            document.querySelector('img#uploadedImage').src = url
        })
});

