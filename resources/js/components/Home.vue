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
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in pokemon">
                                <th scope="row">{{ item.number }}</th>
                                <td>{{ item.name }}</td>
                                <td>
                                    <img :src="item.sprite_url" :alt="item.name">
                                </td>
                                <td>
                                    <button class="btn btn-danger" @click="deleteItem(item.id)">Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <nav>
                            <ul class="pagination">
                                <li class="page-item" :class="{'disabled' : pagination.current_page === 1}">
                                    <button class="page-link" @click="getData(pagination.prev)">Previous</button>
                                </li>
                                <li v-for="page in pageNumbers" class="page-item" :class="{'active': pagination.current_page === page}">
                                    <button class="page-link" @click="getData(`https://www.simple-pokedex.test/pokemon?page=${page}`)">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{'disabled' : pagination.current_page === pagination.last_page}">
                                    <button class="page-link" @click="getData(pagination.next)">Next</button>
                                </li>
                                <li class="page-item"></li>
                            </ul>
                            <em>Page {{ pagination.current_page }} of {{ pagination.last_page }}</em>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
    data () {
        return {
            pokemon: [],
            pagination: {},
            pageOffset: 4,
        }
    },

    mounted () {
        // console.log('Component mounted.');
        this.getData('/pokemon');
    },

    computed: {
        pageNumbers () {
            let from = this.pagination.current_page - this.pageOffset;
            if (from < 1) {
                from = 1;
            }
            let to = from + (this.pageOffset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }
            let pages = [];
            for (let page = from; page <= to; page++) {
                pages.push(page);
            }
            return pages;
        }
    },

    methods: {
        getData (url) {
            axios.get(url)
                .then(response => {
                    // console.log(response.data.data);
                    this.pokemon = response.data.data;
                    this.makePagination(response.data);
                }).catch(error => {
                    console.log(error);
                });
        },

        makePagination (data) {
            this.pagination = {
                current_page: data.meta.current_page,
                last_page: data.meta.last_page,
                first: data.links.first,
                last: data.links.last,
                prev: data.links.prev,
                next: data.links.next,
            };
        },

        deleteItem (id) {
            console.log(id);
            let self = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/pokemon/${id}`)
                        .then(function (response) {
                            self.getData('/pokemon');

                            Swal.fire(
                                'Deleted!',
                                'Your pokemon has been deleted.',
                                'success'
                            );
                        })
                        .catch(function (error) {
                            console.log(error);
                            Swal.fire(
                                'Oops!',
                                'Something went wrong, please try again.',
                                'error'
                            );
                        });
                }
            });
        }
    }
}
</script>
