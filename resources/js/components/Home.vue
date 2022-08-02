<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Simple Pokedex</div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Sprite</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in pokemon">
                                <th scope="row">{{ item.number }}</th>
                                <td>{{ item.name }}</td>
                                <td>
                                    <img :src="item.sprite_url" :alt="item.name">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                pokemon: []
            }
        },

        mounted () {
            console.log('Component mounted.');

            this.getData('/pokemon');
        },

        methods: {
            getData (url) {
                axios.get(url)
                    .then(response => {
                        // console.log(response.data.data);
                        this.pokemon = response.data.data;
                    }).catch(error => {
                        console.log(error);
                        // TODO - handle error
                    });
            },
        }
    }
</script>
