<template>
    <div>
        <v-card flat >
            <v-card-actions>
                <v-subheader>{{ options.message }}</v-subheader>
                <v-spacer></v-spacer>
                <v-btn flat>Печать</v-btn>
                <v-btn flat>в Excel</v-btn>
                <v-btn flat>в Pdf</v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
    export default {
        name: 'test-component',
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
