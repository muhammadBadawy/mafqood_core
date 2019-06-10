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

import java.util.ArrayList;

public class UserInfoActivity extends AppCompatActivity implements AdapterView.OnItemSelectedListener {
    String district[] = null;
    Spinner DistrictSpinner;
    String Selected_district;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_info);

        Spinner GovernorateSpinner = (Spinner) findViewById(R.id.Governorate);
        //DistrictSpinner = (Spinner) findViewById(R.id.District);
        GovernorateSpinner.setOnItemSelectedListener(this);
    }

    @Override
    public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
        if(i==0){
            district=new String[]{"التجمع الخامس - الأحياء والجولف","التجمع الخامس - المستثمرين الجنوبية","التجمع الخامس - المستثمرين الشمالية","التجمع الخامس - الياسمين والبنفسج","التجمع الخامس - جانووب الاكادمية","التجمع الخامس - حي النرجس","التجمع الثالث","االتجمع الأول","ارض الجولف","الدقي","الرحاب","الزاوية الحمراء"," الزمالك","الزيتون","الضاهر","العباسية","العجوزة","القرية الذكية","القطامية","المعادي - الكورنيش","المعادي - جديدة","المعادي القديمة","المقطم","المنيل","المهندسين","النزهة الجديدة","المطرية"," الزمالك","السلام","بدر","جاردن سيتي","حدائق القبة","زهراء المعادي","شبرا","شبرا الخيمة","عين شمس","فيصل","مدينة 15 مايو","مدينة الشروق","مدينة العبور","مدينة نصر - حي السابع - حي الثامن- حي العاشر","مدينة نصر - زهراء","مدينة نصر - مكرم عباس- طياران","مدينة نصر - ملعب الأزهر","مدينتي","مساكن شيراتون","مصر الجديدة","مصر القديمة","مطار القاهرة","وسط البلد"};
        }
        if(i==1){
            district=new String[]{"السادس من أكتوبر - الحصري و الاحياء","السادس من أكتوبر - المنطقة الصناعية","السادس من أكتوبر- طريق الواحات","الشيخ زايد","بولاق","الوراق","الدقي","حدائق الاهرام","العمرانية","الهرم","الحوامدية","البدرشين","كرداسة"};
        }
        if(i==2){
            district=new String[]{"الاسكندرية","المنتزة","العامرية","العجمي","برج العرب","برج العرب الجديد"};
        }
        if(i==3){
            district=new String[]{"بنها","قليوب","شبرا الخيمة","القناطر الخيرية","الخانكة","طوخ","الخصوص"};
        }
        if(i==4){
            district=new String[] {"دمنهور","كفر الدوار","وادي النطرون"};
        }
        if(i==5){
            district=new String[]{"مرسي مطروح","الحمام","العلمين","الضبعة","السلوم","سيوة"};
        }
        if(i==6){
            district=new String[]{"دمياط","راس البر"};
        }
        if(i==7){
            district=new String[]{"الدقهلية","المنصورة","ميت غمر","طلخا","المنزلة","الجمالية","المطرية"};
        }
        if(i==8){
            district=new String[]{"كفر الشيخ","دسوق","بلطيم"};
        }
        if(i==9){
            district=new String[]{"الغربية","طنطا","المحلة","كفر الزيات","زفتي"};
        }
        if(i==10){
            district=new String[]{"المنوفية","شبين الكوم","مدينة السادات","الزقازيق","العاشر من رمضان","منيا القمح","بابيس"};
        }
        if(i==11){
            district=new String[]{"بورسعيد"};
        }
        if(i==12){
            district=new String[] {"الاسماعيلية"};
        }
        if(i==13){
            district=new String[]{"السويس"};
        }
        if(i==14){
            district=new String[]{"شمال سيناء"};
        }
        if(i==15){
            district=new String[]{"شرم الشيخ","دهب"};
        }

        if(i==16){
            district=new String[]{"بني سويف"};
        }
        if(i==17){
            district=new String[]{"الفيوم"};
        }
        if(i==18){
            district=new String[]{"المنيا"};
        }
        if(i==19){
            district=new String[] {"اسيوط"};
        }
        if(i==20){
            district=new String[]{"الوادي الجديد"};
        }
        if(i==21){
            district=new String[]{"الغردقة","راس غارب","سافاجا","مرسي علم"};
        }
        if(i==22){
            district=new String[]{"سوهاج"};
        }
        if(i==23){
            district=new String[]{"قنا"};
        }
        if(i==24){
            district=new String[]{"الاقصر"};
        }
        if(i==25){
            district=new String[]{"اسوان"};
        }
        DistrictSpinner = (Spinner) findViewById(R.id.District);
        ArrayAdapter<String> District_adapter = new ArrayAdapter<String>(this, R.layout.support_simple_spinner_dropdown_item,district);
        DistrictSpinner.setAdapter(District_adapter);

        DistrictSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                Selected_district= (String) DistrictSpinner.getSelectedItem();
//                    Toast.makeText(getApplicationContext(),(String) DistrictSpinner.getSelectedItem() ,Toast.LENGTH_SHORT).show();

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });

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

        if (email.matches(emailPattern)&&phone.matches(phonePattern)){
            //Toast.makeText(getApplicationContext(),massege,Toast.LENGTH_SHORT).show();
        Intent intent = new Intent(this,MainActivity.class);
        Bundle bundle=new Bundle();

            bundle.putString("Email", email);
            bundle.putString("Phone", phone);
            bundle.putString("District", Selected_district);
            intent.putExtras(bundle);

        startActivity(intent);}
    }
}
