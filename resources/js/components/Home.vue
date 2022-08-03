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
                                    <button class="btn btn-primary" @click="editItem(item)">Edit</button>
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

        <div v-if="showEditModal" class="edit-modal">
            <div class="card">
                <div class="card-header">
                    Edit Pokemon
                </div>
                <div class="card-body">
                    <h5 class="card-title">Editing {{ selectedItem.name }}</h5>

                    <label for="number" class="form-label">Number</label>
                    <input v-model="selectedItem.number" name="number" id="number" class="form-control" type="number" step="1" min="0">

                    <label for="name" class="form-label">Name</label>
                    <input v-model="selectedItem.name" name="name" id="name" class="form-control" type="text">

                    <label for="spriteUrl" class="form-label">Sprite Url</label>
                    <input v-model="selectedItem.sprite_url" name="sprite_url" id="spriteUrl" class="form-control" type="text">
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" @click="saveItem(selectedItem.id)">Save</button>
                    <button class="btn btn-danger" @click="closeEditModal">Cancel</button>
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
            showEditModal: false,
            selectedItem: null,
        }
    },

    mounted () {
        this.getData('/pokemon');
    },

    computed: {
        /**
         * Calculates the paginated data page numbers.
         *
         * @returns {*[]}
         */
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
        /**
         * Gets the data from the API.
         *
         * @param url
         */
        getData (url) {
            axios.get(url)
                .then(response => {
                    this.pokemon = response.data.data;
                    this.makePagination(response.data);
                }).catch(error => {
                    console.log(error);
                });
        },

        /**
         * Creates the pagination object from response data.
         *
         * @param data
         */
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

        /**
         * Closes the modal.
         */
        closeEditModal () {
            this.showEditModal = false;
            this.selectedItem = null;
        },

        /**
         * Opens the edit item modal.
         *
         * @param item
         */
        editItem (item) {
            this.selectedItem = structuredClone(item);
            this.showEditModal = true;
        },

        /**
         * Submits ajax request to API put endpoint.
         *
         * @param id
         */
        saveItem (id) {
            let self = this;

            axios.put(`/pokemon/${id}`, this.selectedItem)
                .then(function (response) {
                    self.getData('/pokemon');
                    self.showEditModal =false;

                    Swal.fire(
                        'Updated!',
                        'Your pokemon has been updated.',
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
        },

        /**
         * Submits ajax request to API destroy endpoint.
         *
         * @param id
         */
        deleteItem (id) {
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

<style scoped>
.edit-modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1060;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
