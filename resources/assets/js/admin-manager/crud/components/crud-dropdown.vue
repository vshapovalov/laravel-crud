<template>
    <v-select
            :items="rows"
            :item-value="'key'"
            :item-text="'value'"
            v-model="selected"
            :multiple="!isSingleMode"
            max-height="400"
            class="pt-0"
            solo
            :value-comparator="compareValues"
            clearable
    ></v-select>
</template>

<script>
    export default {
        name: 'crud-dropdown',
        model: {
            prop: "value",
            event: "change"
        },
        props: {
            value: {},
            field: {}
        },
        data: function () {
            return {
                selected: '',
                rows: [],
            }
        },
        watch:{
            value(val, oldVal){
                if (oldVal === undefined) this.parseValue();
            },
            selected(val, oldVal){
                this.emitChanges();
            },
        },
        computed: {
            isSingleMode(){

                return !(this.field.additional && (this.field.additional.mode === 'multi'));
            },
        },
        methods: {
            compareValues(a,b){
                return a==b;
            },
            emitChanges(){

                this.$emit('change', this.isSingleMode ? this.selected : JSON.stringify(this.selected) );
            },
            parseValue(){

                if (this.field.additional && this.field.additional.values){

                    if (!this.rows.length)
                        this.rows = _.cloneDeep(this.field.additional.values);

                    if (this.value){

                        if (this.isSingleMode) {

                            this.selected = this.value;
                        } else {

                            this.selected = _.isString(this.value) ?  JSON.parse(this.value) : this.value;
                        }
                    }
                }
            },
        },
        mounted() {

            this.parseValue();
        },
    }
</script>
