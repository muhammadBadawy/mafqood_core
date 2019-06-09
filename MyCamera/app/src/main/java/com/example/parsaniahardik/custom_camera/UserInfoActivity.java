package com.example.parsaniahardik.custom_camera;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import android.view.View.OnClickListener;

public class UserInfoActivity extends AppCompatActivity implements AdapterView.OnItemSelectedListener {
    String district[] = null;
    Spinner DistrictSpinner;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_info);
        Spinner GovernorateSpinner = (Spinner) findViewById(R.id.Governorate);
        DistrictSpinner = (Spinner) findViewById(R.id.District);
        GovernorateSpinner.setOnItemSelectedListener(this);
    }

    @Override
    public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
        if(i==0){
            district=new String[]{"11","12","13"};
        }
        if(i==1){
            district=new String[]{"21","22","23"};
        }
        if(i==2){
            district=new String[]{"31","32","33"};
        }
        if(i==3){
            district=new String[]{"41","42","43"};
        }
        ArrayAdapter<String> District_adapter = new ArrayAdapter<String>(this, R.layout.support_simple_spinner_dropdown_item,district);
        DistrictSpinner.setAdapter(District_adapter);
    }


    @Override
    public void onNothingSelected(AdapterView<?> adapterView) {

    }

    public void onClickBtn(View view) {
        EditText emailValidate = (EditText)findViewById(R.id.email);
        EditText phoneValidate = (EditText)findViewById(R.id.phone);

        String email = emailValidate.getText().toString().trim();
        String phone = phoneValidate.getText().toString().trim();;

        String emailPattern = "[a-zA-Z0-9._-]+@[a-z]+\\.+[a-z]+";
        String phonePattern = "(01)[0-9]{9}$";
        String massege ="";

// onClick of button perform this simplest code.
        if (email.matches(emailPattern)) {massege += " valid email address ";} else { massege +=" Invalid email address ";}
        if(phone.matches(phonePattern)){ massege +="and valid phone number"; } else { massege+="and Invalid phone number"; }
        Toast.makeText(getApplicationContext(),massege,Toast.LENGTH_SHORT).show();
        if (email.matches(emailPattern)&&phone.matches(phonePattern)){
        Intent intent = new Intent(this,MainActivity.class);
        startActivity(intent);}
    }
}
