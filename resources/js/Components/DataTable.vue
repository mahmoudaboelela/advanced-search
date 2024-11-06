

<template>
  <filter-form :field-items="fields" @filters-changed="onFiltersChanged"
  ></filter-form>


  <v-card>
    <v-card-title>Dynamic Data Table with API Fetch</v-card-title>

    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="itemsPerPage"
      :loading="loading"
      @update="fetchData"
    >
        <template #item.active="{ item }">
            <v-chip
                :color="item.active ? 'green' : 'red'"
                dark
            >
                {{ item.active ? 'Active' : 'Inactive' }}
            </v-chip>
        </template>
        <template #item.created_at="{ item }">
            <span>{{ formatDate(item.created_at) }}</span>
        </template>
        <template #item.updated_at="{ item }">
            <span>{{ formatDate(item.updated_at) }}</span>
        </template>
    </v-data-table>
  </v-card>
</template>
<script>
import axios from 'axios';
import FilterForm from "@/components/FilterForm.vue";
import moment from 'moment';

export default {
  components: {FilterForm},
    data() {
        return {
            headers: [],
            items: [],           // data items for the table
            itemsPerPage: 10,       // total items count for pagination
            loading: false,      // Loading
            fields: [],
        };
    },
  methods: {
    async fetchData(filters=[]) {
      this.loading = true;
      try {
        let params = {
            filters
        };
        let response = await axios.get('/api/customers', { params });
        this.items = response.data.data;
        this.headers = response.data.headers;
        this.fields = response.data.fields;
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        this.loading = false;
      }
    },
      onFiltersChanged(filters){
          this.fetchData(filters);
      },
      formatDate(date) {
          return moment(date).format('YYYY-MM-DD');
      }
  },
  mounted() {
    this.fetchData().then(()=>{
    }); // initial data fetch

  }
};
</script>


<style scoped lang="sass">

</style>
