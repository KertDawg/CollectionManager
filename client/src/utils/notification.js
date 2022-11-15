import { Notify } from "quasar";


var NotificationsObject = {
    ShowSuccess: function(Message)
    {
        Notify.create({
            message: Message,
            icon: "sentiment_satisfied",
            color: "positive",
        });
    },

    ShowFailure: function(Message)
    {
        Notify.create({
            message: Message,
            icon: "sentiment_very_dissatisfied",
            color: "negative",
        });
    },
};
  
export default NotificationsObject;
