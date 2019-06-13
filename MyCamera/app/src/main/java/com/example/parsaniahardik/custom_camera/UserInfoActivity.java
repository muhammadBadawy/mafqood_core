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
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class UserInfoActivity extends AppCompatActivity implements AdapterView.OnItemSelectedListener {
    List<String> district = null;
    String[] item;
    Spinner DistrictSpinner;
    String Selected_district;
    String Selected;
    Map<String, String> map;
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
        map = new HashMap<>();
        district=new ArrayList<>();
        if(i==0){
            map.put("مصر", "131");
        }
        if(i==1){
            map.put("التجمع الخامس - الأحياء والجولف","1");
            map.put("التجمع الخامس - المستثمرين الجنوبية","2");
            map.put("التجمع الخامس - المستثمرين الشمالية","3");
            map.put("التجمع الخامس - الياسمين والبنفسج","5");
            map.put("التجمع الخامس - جانووب الاكادمية","6");
            map.put("التجمع الخامس - حي النرجس","7");
            map.put("التجمع الثالث","8");
            map.put("االتجمع الأول","9");
            map.put("ارض الجولف","10");
            map.put("الدقي","11");
            map.put("الرحاب","12");
            map.put("الزاوية الحمراء","13");
            map.put(" الزمالك","14");
            map.put("الزيتون","15");
            map.put("الضاهر","16");
            map.put("العباسية","17");
            map.put("العجوزة","18");
            map.put("القرية الذكية","19");
            map.put("القطامية","20");
            map.put("المعادي - الكورنيش","21");
            map.put("المعادي - جديدة","22");
            map.put("المعادي القديمة","23");
            map.put("المقطم","24");
            map.put("المنيل","25");
            map.put("المهندسين","26");
            map.put("النزهة الجديدة","27");
            map.put("المطرية","28");
            map.put(" الزمالك","29");
            map.put("السلام","30");
            map.put("بدر","31");
            map.put("جاردن سيتي","32");
            map.put("حدائق القبة","33");
            map.put("زهراء المعادي","34");
            map.put("شبرا","35");
            map.put("شبرا الخيمة","36");
            map.put("عين شمس","37");
            map.put("فيصل","38");
            map.put("مدينة 15 مايو","39");
            map.put("مدينة الشروق","40");
            map.put("مدينة العبور","41");
            map.put("مدينة نصر - حي السابع - حي الثامن- حي العاشر","42");
            map.put("مدينة نصر - زهراء","43");
            map.put("مدينة نصر - مكرم عباس- طياران","44");
            map.put("مدينة نصر - ملعب الأزهر","45");
            map.put("مدينتي","46");
            map.put("مساكن شيراتون","47");
            map.put("مصر الجديدة","48");
            map.put("مصر القديمة","49");
            map.put("مطار القاهرة","50");
            map.put("وسط البلد","51");
        }
        if(i==2){
            map.put( "السادس من أكتوبر - الحصري و الاحياء","52");
            map.put("السادس من أكتوبر - المنطقة الصناعية","53");
            map.put( "السادس من أكتوبر- طريق الواحات","54");
            map.put( "الشيخ زايد","55");
            map.put( "بولاق","56");
            map.put( "الوراق","57");
            map.put( "الدقي","58");
            map.put( "حدائق الاهرام","59");
            map.put( "العمرانية","60");
            map.put( "الهرم","61");
            map.put( "الحوامدية","62");
            map.put( "البدرشين","63");
            map.put( "كرداسة","64");
        
        }
        if(i==3){
            map.put("الاسكندرية","65");
            map.put("المنتزة","66");
            map.put("العامرية","67");
            map.put("العجمي","68");
            map.put("برج العرب","69");
            map.put("برج العرب الجديد","70");
        }
        if(i==4){
            map.put("بنها","71");
            map.put("قليوب","72");
            map.put("شبرا الخيمة","73");
            map.put("القناطر الخيرية","74");
            map.put("الخانكة","75");
            map.put("طوخ","76");
            map.put("العبور","77");
            map.put("الخصوص","78");
        }
        if(i==5){
            map.put("دمنهور","79");
            map.put("كفر الدوار","80");
            map.put("وادي النطرون","81");
        }
        if(i==6){
            map.put( "مرسي مطروح","82");
            map.put("الحمام","83");
            map.put("العلمين","84");
            map.put("الضبعة","85");
            map.put( "السلوم","86");
            map.put("سيوة","87");
        }
        if(i==7){
            district=new ArrayList<>();
            map.put("دمياط", "88");
            map.put("راس البر", "89");
        }
        if(i==8){
            map.put("الدقهلية","90");
            map.put( "المنصورة","91");
            map.put( "ميت غمر","92");
            map.put("طلخا","93");
            map.put("المنزلة","94");
            map.put("الجمالية","95");
            map.put("المطرية","96");
        }
        if(i==9){
            map.put("كفر الشيخ","97");
            map.put("دسوق","98");
            map.put("بلطيم","99");
        }
        if(i==10){
            map.put("الغربية","100");
            map.put("طنطا","101");
            map.put("المحلة","102");
            map.put("كفر الزيات","103");
            map.put("زفتي","104");
        }
        if(i==11){
            map.put("المنوفية","105");
            map.put("شبين الكوم","106");
            map.put("مدينة السادات","107");
            map.put("الزقازيق","108");
            map.put("العاشر من رمضان","109");
            map.put("منيا القمح","110");
            map.put("بابيس","111");
        }
        if(i==12){
            map.put("بورسعيد","112");
        }
        if(i==13){
            map.put("الاسماعيلية","113");
        }
        if(i==14){
            map.put("السويس","114");
        }
        if(i==15){
            map.put("شمال سيناء","115");
        }
        if(i==16){
            map.put("شرم الشيخ","116");
            map.put( "دهب","117");
        }

        if(i==17){
            map.put("بني سويف","118");
        }
        if(i==18){
            map.put("الفيوم","119");
        }
        if(i==19){
            map.put("المنيا","120");
        }
        if(i==20){
            map.put("اسيوط","121");
        }
        if(i==21){
            map.put("الوادي الجديد","122");
        }
        if(i==22){
            map.put("الغردقة","123");
            map.put("راس غارب","124");
            map.put("سافاجا","125");
            map.put("مرسي علم","126");
        }
        if(i==23){
            map.put("سوهاج","127");
        }
        if(i==24){
            map.put("قنا","128");
        }
        if(i==25){
            map.put("الاقصر","129");
        }
        if(i==26){
            district=new ArrayList<>();
            map.put("اسوان", "130");
        }
        for (Map.Entry<String, String> entry : map.entrySet()) {
            district.add(entry.getKey());
        }
        item = district.toArray(new String[district.size()]);

        DistrictSpinner = (Spinner) findViewById(R.id.District);
        ArrayAdapter<String> District_adapter = new ArrayAdapter<String>(this, R.layout.support_simple_spinner_dropdown_item,item);
        DistrictSpinner.setAdapter(District_adapter);

        DistrictSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                Selected= (String) DistrictSpinner.getSelectedItem();
                Selected_district=map.get(Selected);
                //System.out.println("Key : " + entry.getKey() + " Value : " + entry.getValue());


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
