<template>
    <v-card class="mb-3">
        <v-row justify="center" align="center">
            <v-col class="mt-2">
                <v-select
                    label="Field"
                    :items="fieldItems"
                    v-model="selectedField"
                ></v-select>
            </v-col>
            <v-col class="mt-2">
                <v-select
                    label="Operator"
                    :items="operatorItems"
                    :disabled="selectedField === '' || selectedField === null"
                    v-model="selectedOperator"
                ></v-select>
            </v-col>
            <v-col class="mt-2"
                   v-if="currentSelectedField.type==='enum' && currentSelectedField.options.length > 0"
            >
                <v-select
                          label="Value"
                          :items="currentSelectedField.options"
                          v-model="selectedValue"
                          :multiple="selectedOperator === 'in'"
                          :disabled="selectedOperator === '' || selectedOperator === null"
                ></v-select>
            </v-col>
            <v-col class="mt-2"
                   v-else-if="currentSelectedField.type === 'text'"
            >

                <v-text-field
                    v-model="selectedValue"
                    :disabled="selectedOperator === '' || selectedOperator === null"
                    label="Query"></v-text-field>

            </v-col>
            <v-col class="mt-2"
                   v-else-if="currentSelectedField.type=== 'date'"
            >

                <v-date-input
                    multiple="range" label="Date input" v-model="selectedValue"
                    :disabled="selectedOperator === '' || selectedOperator === null"
                ></v-date-input>
            </v-col>
            <v-col class="mt-2"
                   v-else-if="currentSelectedField.type=== 'number'"
            >
                <v-number-input
                    v-model="selectedValue"
                    :disabled="selectedOperator === '' || selectedOperator === null"
                    control-variant="default"
                    min="1"
                    inset
                ></v-number-input>
            </v-col>
            <v-col>
                <v-btn class=" mr-2" @click="addFilter"
                       prepend-icon="mdi-filter"
                       size="large"
                >
                    Add
                </v-btn>
                <v-btn class="" @click="clearFilters"
                       prepend-icon="mdi-filter-remove"
                       size="large"
                       color="error"
                >
                    Clear
                </v-btn>
            </v-col>

        </v-row>

<!--        applied filters-->
        <v-row class="mb-3 mt-1 ml-1">
            <div v-for="(filter, index) in filters" :key="index" class="text-center">
                <v-chip
                    class="ma-2"
                    closable
                    @click:close="removeFilter(index)"
                >
                    {{ filter.label }}
                </v-chip>
            </div>
        </v-row>
    </v-card>

<!--    dialog-->
    <template>
        <div class="text-center pa-4">

            <v-dialog
                v-model="dialog"
                width="auto"
            >
                <v-card
                    max-width="400"
                    prepend-icon="mdi-close-thick"
                    text="You have to fill all inputs of the filter."
                    title="Please fill all required inputs"
                >
                    <template v-slot:actions>
                        <v-btn
                            class="ms-auto"
                            text="Ok"
                            @click="dialog = false"
                        ></v-btn>
                    </template>
                </v-card>
            </v-dialog>
        </div>
    </template>
    <!--    dialog-->

</template>

<script>
import moment from "moment";

export default {
    props: {
        fieldItems: Array,
    },
    data() {
        return {
            operatorItems: [
                {title: "Equals", value: "="},
                {title: "Contains", value: "contains"},
                {title: "Not Equals", value: "!="},
                {title: "Greater Than", value: ">"},
                {title: "Less Than", value: "<"},
                {title: "Greater Than or Equal To", value: ">="},
                {title: "Less Than or Equal To", value: "<="},
                {title: "Between", value: "between"},
                {title: "IN", value: "in"},
            ],
            selectedField: "",
            selectedOperator: "",
            selectedValue: null,
            currentSelectedField: {
                type:'text'
            },
            filters: [],
            dialog:false
        }
    },
    emits: ['filters-changed'],
    methods: {
        addFilter() {
            if (!this.validateInputs())
                return;
            this.filters.push({
                field: this.selectedField,
                value: this.selectedValue,
                operator: this.selectedOperator,
                type: this.currentSelectedField.type,
                label: this.currentSelectedField.title + " " + this.selectedOperator + " " + (this.currentSelectedField.type === 'date' ? this.formatDate(this.selectedValue) : this.selectedValue)
            })
            this.emitFilters()
        },
        removeFilter(index) {
            this.filters.splice(index, 1);
            this.emitFilters()
        },
        clearFilters(){
            this.filters.splice(0, this.filters.length);
            this.$emit('filters-changed', this.filters);
            this.clearInputs()
        },
        formatDate(dateRange) {
            let startDate = moment(dateRange[0]);
            let endDate = moment(dateRange[dateRange.length - 1]);
            return `${startDate.format('MMM D, YYYY')} - ${endDate.format('MMM D, YYYY')}`;
        },
        clearInputs(){
            this.selectedField = null;
            this.selectedOperator = null;
            this.selectedValue = null;
        },
        validateInputs(){
            if (this.selectedField === null || this.selectedOperator ===  null || this.selectedValue === null){
                this.dialog = true;
                return false;
            }
            return true;
        },
        emitFilters() {
            this.$emit('filters-changed', this.filters);
        },

    },
    watch: {
        selectedField() {
            this.currentSelectedField = this.fieldItems.find(item => item.value === this.selectedField) ?? {type:'text'}
            this.selectedOperator = null;
            this.selectedValue = null;
        },
    }
}

</script>


<style scoped lang="sass">

</style>
