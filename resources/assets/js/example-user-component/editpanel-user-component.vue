<template>
    <div class="layout align-center">
        <v-spacer></v-spacer>
        <v-btn flat color="success">Print</v-btn>
        <v-btn flat color="green">to Excel</v-btn>
        <v-btn flat color="red">to Pdf</v-btn>
    </div>
</template>

<script>
    export default {
        name: 'editpanel-user-component',
        props: [ 'options' ],
        data: function () {
            return {
                message: 'Hello, i\'m test component, and this is my counter',
                counter: 1
            }
        },
        mounted(){
            this.counter = this.options.counterStartValue;
        }
    }
</script>
