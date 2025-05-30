// resources/js/utils/message.ts

import { toast } from 'vue-sonner';
import 'vue-sonner/style.css'

export function showSuccessMessage(title: string, description?: string) {
  toast.success(description || title, {
    description: description ? title : undefined,
    duration: 3000,
  });
}

export function showErrorMessage(title: string, description?: string) {
  toast.error(description || title, {
    description: description ? title : undefined,
    duration: 3000,
  });
}
