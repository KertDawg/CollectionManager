var ErrorObject = {
  ERROR_LEVEL_INFO: 1,
  ERROR_LEVEL_WARNING: 2,
  ERROR_LEVEL_FATAL: 3,

  HandleError: function (Message, ErrorLevel)
  {
    //  Are we in a dev environment?  If so, alert the user.
    if (process.env.DEV)
    {
      alert(Message);
    }
    else
    {
      console.log(Message);
    }
  }
};


export default ErrorObject;
