import { ComboBoxCustomer, comboBoxDoctor, TypePaymens } from '@/interface/ComboBox';
import { comboBoxServices } from '@/services/comboBoxServices';
import { ref } from 'vue';

export function useComboBox() {
    const comboBoxCustomerData = ref([] as ComboBoxCustomer[]);
    const comboBoxDoctorData = ref([] as comboBoxDoctor[]);
    const typePaymentData = ref([] as TypePaymens[]);
    const loadingComboBoxCustomer = async (texto: string) => {
        try {
            const response = await comboBoxServices.getCustomer(texto);
            comboBoxCustomerData.value = response;
        } catch (error) {
            console.error('Error loading customer combo box:', error);
        }
    };
    const loadingComboBoxDoctor = async (texto: string = '') => {
        try {
            const response = await comboBoxServices.getDoctor(texto);
            comboBoxDoctorData.value = response;
        } catch (error) {
            console.error('Error loading doctor combo box:', error);
        }
    };

    const loadingTypePayment = async () => {
        try {
            const response = await comboBoxServices.getTypePayment();
            typePaymentData.value = response;
        } catch (error) {
            console.error('Error loading type payment combo box:', error);
        }
    };
    return {
        comboBoxCustomerData,
        comboBoxDoctorData,
        typePaymentData,
        loadingComboBoxCustomer,
        loadingComboBoxDoctor,
        loadingTypePayment,
    };
}
