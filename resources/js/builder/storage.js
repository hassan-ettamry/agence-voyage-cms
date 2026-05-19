window.BuilderStorage = {

    /*
    |--------------------------------------------------------------------------
    | Save Page
    |--------------------------------------------------------------------------
    */

    async save() {

        try {

            /*
            |--------------------------------------------------------------------------
            | Get Structure From State
            |--------------------------------------------------------------------------
            */

            const structure =
                BuilderStore.getStructure();

            /*
            |--------------------------------------------------------------------------
            | Form Data
            |--------------------------------------------------------------------------
            */

            const formData = new FormData();

            formData.append('_method', 'PUT');

            formData.append(
                'structure',
                JSON.stringify(structure)
            );

            /*
            |--------------------------------------------------------------------------
            | Request
            |--------------------------------------------------------------------------
            */

            const response = await fetch(

                `/pages/${window.pageId}`,

                {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),

                        'Accept': 'application/json'
                    },

                    body: formData
                }

            );

            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                console.error('Save failed');

                alert('Save failed');

                return;
            }

            const data = await response.json();

            console.log(data);

            alert('Page saved successfully');

        } catch (error) {

            console.error(error);

            alert('Error while saving');

        }

    }

};
