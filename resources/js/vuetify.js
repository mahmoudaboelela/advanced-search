import { createVuetify } from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css'

import { VDateInput } from 'vuetify/labs/VDateInput';
import { VNumberInput } from 'vuetify/labs/VNumberInput'

export default createVuetify({
    components:{
        ...components,
        VDateInput,
        VNumberInput
    },
    directives,
    theme: {
        defaultTheme: 'dark'
    },
});
